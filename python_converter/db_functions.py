import pandas as pd
from sqlalchemy import create_engine, MetaData, update, text
import os
from dotenv import load_dotenv

load_dotenv()
username = os.getenv('DB_USERNAME')
password = os.getenv('DB_PASSWORD')
host = os.getenv('DB_HOST')
port = os.getenv('DB_PORT')
database = os.getenv('DB_DATABASE')
table_name = "adverts"

engine = create_engine(f'mysql+pymysql://{username}:{password}@{host}:{port}/{database}', connect_args={"autocommit": True})

def add_dataframe_to_db(data: pd.DataFrame, user_id:int, deactivate_old_ad: bool):

    if deactivate_old_ad:
        metadata = MetaData()
        metadata.reflect(bind=engine)
        table = metadata.tables[table_name]
        with engine.connect() as connection:
            stmt = (
                update(table)
                .where(table.c.user_id == user_id)
                .values(status_ad='not_activ')
            )
            connection.execute(stmt)
            connection.commit()

    # Добавляем данные в таблицу
    # index=False предотвращает добавление индекса DataFrame в таблицу базы данных
    data.to_sql(table_name, con=engine, if_exists='append', index=False)

def add_task_to_db(status: str, user_id: int, filename: str) -> int:
    query = """
        INSERT INTO converting_queue (user_id, filename, status)
        VALUES (:user_id, :filename, :status);
    """
    with engine.connect() as connection:
        result = connection.execute(
            text(query), {"user_id": user_id, "filename": filename, "status": status}
        )
        task_id = result.lastrowid  # Получение ID последней вставленной строки
    return task_id

def update_task_status(task_id: int, status: str, error_msg: str = None):
    query = """
        UPDATE converting_queue
        SET status = :status, error_msg = :error_msg
        WHERE task_id = :task_id;
    """
    with engine.connect() as connection:
        connection.execute(
            text(query), {"task_id": task_id, "status": status, "error_msg": error_msg}
        )

def get_status_by_task_id(task_id: int) -> dict:
    query = """
        SELECT * FROM converting_queue
        WHERE task_id = :task_id;
    """
    with engine.connect() as connection:
        result = connection.execute(text(query), {"task_id": task_id})
        row = result.fetchone()
    return dict(row._mapping) if row else None

def get_queue_position() -> int:
    query = """
        SELECT COUNT(*) AS queue_count
        FROM converting_queue
        WHERE status IN ('waiting', 'in_progress');
    """
    with engine.connect() as connection:
        result = connection.execute(text(query))
        queue_count = result.scalar()  # Получение первой строки первого столбца
    return queue_count

'''
CREATE TABLE converting_queue (
    task_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    created DATETIME DEFAULT CURRENT_TIMESTAMP,
    filename VARCHAR(255) NOT NULL,
    status ENUM('waiting', 'in_progress', 'done', 'error') NOT NULL,
    error_msg TEXT NULL
);
'''
