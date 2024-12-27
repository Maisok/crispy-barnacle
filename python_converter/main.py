import logging
from io import BytesIO
from json import loads

import uvicorn
from fastapi import FastAPI, File, UploadFile, HTTPException, BackgroundTasks
from fastapi.middleware.cors import CORSMiddleware
from fastapi.responses import RedirectResponse

from db_functions import add_dataframe_to_db
from db_functions import add_task_to_db, update_task_status, get_status_by_task_id, get_queue_position
from parser import parse_file_to_df

logging.basicConfig(level=logging.INFO)


DEBUG = True
MAX_QUEUE_SIZE = 100  # Максимальный размер очереди

app = FastAPI()

app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],  # Укажите конкретные домены вместо "*", если нужно
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)


def log(msg):
    logging.info(msg)

@app.get("/")
async def root():
    """
    Перенаправление на документацию
    """
    return RedirectResponse("/docs")

@app.get("/get_status")
async def get_status(task_id: int):
    """Получить статус задачи по `task_id`."""
    status = get_status_by_task_id(task_id)
    if status:
        return {"task_id": task_id, "status": status["status"], "error_msg": status["error_msg"]}
    raise HTTPException(status_code=404, detail="Task not found")


@app.post("/upload")
async def upload_data(user_id: int, columns: str, background_tasks: BackgroundTasks, file: UploadFile = File(...), skip_rows: int = 0, csv_encoding: str = "auto", csv_delimiter: str = ",", add_sheet_name_to_product_name: bool = True, extract_data_from_product_name: bool = True, skip_empty_price_rows=True, deactivate_old_ad=True, split_symbols=" ", selected_brands: str = ""):
    """
       Отправляет файл в очередь на обработку.

       Параметры:
       - `user_id` (int): Идентификатор пользователя, от имени которого выполняется загрузка.
       - `columns` (str): Json-строка с перечнем колонок, необходимых для обработки данных. В качестве ключей используются названия колонок в бд, в качестве значения может использоваться название колонки в файле или индекс.
       - `file` (UploadFile): Загружаемый файл csv, json xls или xlsx формата. Передаётся в body запроса как form-data.
       - `skip_rows` (int, по умолчанию 0): Количество строк, которые нужно пропустить в начале файла.
       - `csv_encoding` (str, по умолчанию "auto"): Кодировка csv файла. Если "auto", определяется автоматически.
       - `csv_delimiter` (str, по умолчанию ","): Разделитель колонок для csv файлов.
       - `add_sheet_name_to_product_name` (bool, по умолчанию True): Добавлять ли имя листа Excel в название продукта для произведения поиска.
       - `extract_data_from_product_name` (bool, по умолчанию True): Извлекать ли дополнительные данные из имени продукта.
       - `skip_empty_price_rows` (bool, по умолчанию True): Пропускать ли строки с пустыми значениями цены.
       - `deactivate_old_ad` (bool, по умолчанию True): Выставить ли для старых обявлений статус 'not_activ'
       - `split_symbols` (str, по умолчанию " "): Символы, используемые для разделения слов в названии продукта. Например, если указать " /", данные из названия продукта будут делиться на слова по пробелам или слешам.
       - `selected_brands` (str, по умолчанию ""): Фильтр брендов, указанных пользователем (из настроек). Json-строка массив.
       """
    try:
        columns = loads(columns)
    except Exception as e:
        raise HTTPException(status_code=400, detail=f"Invalid JSON for param 'columns': {str(e)}")

    try:
        if selected_brands:
            selected_brands = loads(selected_brands)
            if not isinstance(selected_brands, list):
                raise ValueError("JSON string should contain an array, not an object")
    except Exception as e:
        raise HTTPException(status_code=400, detail=f"Invalid JSON for param 'selected_brands': {str(e)}")

    queue_position = get_queue_position()
    if queue_position >= MAX_QUEUE_SIZE:
        raise HTTPException(
            status_code=503,
            detail="Server is too busy. Please try again later."
        )

    file_stream = BytesIO(await file.read())

    task_id = add_task_to_db("waiting", user_id, file.filename)

    # Запускаем фоновую обработку
    background_tasks.add_task(process_file, task_id, user_id, columns, file_stream, file.filename, skip_rows, csv_encoding, csv_delimiter,
                              add_sheet_name_to_product_name, extract_data_from_product_name, skip_empty_price_rows, deactivate_old_ad, split_symbols, selected_brands)

    return {"message": "File added to the queue", "task_id": task_id, "queue_position": queue_position}

def process_file(task_id:int, user_id: int, columns: dict, file_stream, filename, skip_rows: int, encoding: str, delimiter: str, add_sheet_name_to_product_name: bool, extract_data_from_product_name: bool, skip_empty_price_rows, deactivate_old_ad, split_symbols, selected_brands):

    # import time
    # time.sleep(5)
    # update_task_status(task_id, "in_progress")
    # time.sleep(8)
    # with open("temp/"+str(task_id)+".txt", "w") as f:
    #     f.write("hi")
    # update_task_status(task_id, "done")

    """Фоновая обработка файла."""
    df = None
    try:
        update_task_status(task_id, "in_progress")
        df = parse_file_to_df(file_stream, filename, user_id, columns, skip_rows, encoding, delimiter,
                              add_sheet_name_to_product_name, extract_data_from_product_name, skip_empty_price_rows, split_symbols, selected_brands)
    except Exception as e:
        error_msg = f"Error while parsing file {filename}: {type(e).__name__} {str(e)}"
        update_task_status(task_id, "error", error_msg)
        log(error_msg)
        return

    try:
        if df is not None:
            add_dataframe_to_db(df, user_id, deactivate_old_ad)
        update_task_status(task_id, "done")
    except Exception as e:
        error_msg = f"Error while inserting into the db: {type(e).__name__} {str(e)}"
        update_task_status(task_id, "error", error_msg)
        log(error_msg)


if __name__ == "__main__":
    uvicorn.run("main:app", port=8002, host="0.0.0.0", workers=8, log_level="debug" if DEBUG else "")

