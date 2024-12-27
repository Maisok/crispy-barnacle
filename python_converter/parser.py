import pandas as pd
import chardet
import re
from car_specs import brand_to_models, model_to_body_to_year, brand_to_engines, car_brands, brand_rus_to_en, model_rus_to_en


def parse_file_to_df(file, filename, user_id: int, column_names: dict, skip_rows, encoding, delimiter,
                     add_sheet_name_to_product_name, extract_data_from_product_name, skip_empty_price_rows, split_symbols, selected_brands):

    data = 0
    is_excel = False
    extension = filename.rsplit(".")[-1].lower()

    if extension == 'csv':
        if encoding == "auto":
            if file is str:
                with open(file, 'rb') as f:
                    result = chardet.detect(f.read())
            else:
                result = chardet.detect(file.read())
                file.seek(0)
            encoding = result['encoding']
        data = pd.read_csv(file, encoding=encoding, delimiter=delimiter, skiprows=skip_rows)

    elif extension == 'json':
        data = pd.read_json(file)

    elif extension in ['xls', 'xlsx']:
        # Read all sheets in Excel file into a dictionary of DataFrames
        is_excel = True
        excel_file = pd.ExcelFile(file)
        sheet_names = excel_file.sheet_names

        data = dict()
        for sheet in sheet_names:
            df = excel_file.parse(sheet, skiprows=skip_rows)
            for sheet1 in data:
                if len(data[sheet1].columns) != len(df.columns):
                    raise ValueError("Only the same number of columns in sheets are supported")
            data[sheet] = df

    else:
        raise ValueError("Unsupported file extension.")

    result_df = pd.DataFrame()

    if is_excel:
        for sheet in data:
            add_name = ""
            if add_sheet_name_to_product_name:
                add_name = sheet
            df = format_df_for_db(data[sheet], user_id, column_names, add_name, extract_data_from_product_name, skip_empty_price_rows, split_symbols, selected_brands)
            result_df = pd.concat([result_df, df], ignore_index=True)

    else:
        # if not excel
        result_df = format_df_for_db(data, user_id, column_names, "", extract_data_from_product_name, skip_empty_price_rows, split_symbols, selected_brands)

    return result_df


def format_df_for_db(data: pd.DataFrame, user_id: int, column_names: dict, add_to_product_name: str, extract_data_from_product_name: bool, skip_empty_price_rows: bool, split_symbols:str, selected_brands: list):
    right_columns = ['user_id', 'art_number', 'product_name', 'new_used', 'brand', 'model', 'body', 'number', 'engine',
               'year', 'L_R', 'F_R', 'U_D', 'color', 'applicability', 'quantity', 'price', 'availability',
               'delivery_time', 'data', 'status_ad', 'id_ad', 'created_at', 'updated_at', 'new_column',
               'another_column', 'main_photo_url', 'additional_photo_url_1', 'additional_photo_url_2',
               'additional_photo_url_3']
    string_columns = ['art_number', 'product_name', 'new_used', 'brand', 'model', 'body', 'number', 'engine',
                     'L_R', 'F_R', 'U_D', 'color', 'applicability', 'quantity', 'availability',
                     'delivery_time', 'id_ad', 'new_column',
                     'main_photo_url', 'additional_photo_url_1', 'additional_photo_url_2',
                     'additional_photo_url_3']

    result_df = pd.DataFrame(columns=right_columns)

    for col in right_columns:
        if col in column_names.keys():
            current_column = column_names[col]
            if isinstance(current_column, int) and current_column < len(data.columns):
                current_column = data.columns[current_column]
            if current_column in data.columns:
                result_df[col] = data[current_column]
            else:
                result_df[col] = pd.NA  # Для отсутствующих столбцов ставим NaN (или pd.NA)
        else:
            result_df[col] = pd.NA  # Для отсутствующих столбцов ставим NaN (или pd.NA)

    result_df['user_id'] = int(user_id)
    result_df['status_ad'] = 'activ'

    # Приведение столбцов в нужный формат

    # Преобразование всех строковых значений в нижний регистр и удаление NaN
    for col in string_columns:
        result_df[col] = result_df[col].fillna('').astype(str).str.lower()

    price = result_df['price'].fillna("").astype(str).str.replace(",", ".").str.replace(r"[^\d.]", "", regex=True)
    result_df['price'] = pd.to_numeric(price, errors='coerce').astype(float)
    if skip_empty_price_rows: # удаление NaN и нулевых цен
        result_df = result_df[result_df['price']>0]

    result_df['data'] = pd.to_datetime(result_df['data'], errors='coerce')
    result_df['created_at'] = pd.to_datetime(result_df['created_at'], errors='coerce')
    result_df['updated_at'] = pd.to_datetime(result_df['updated_at'], errors='coerce')

    result_df['another_column'] = pd.to_numeric(result_df['another_column'], errors='coerce').fillna(0).astype(int)

    result_df['year'] = result_df['year'].fillna("").apply(lambda x: str(int(x)) if x != "" else x).str.replace(r"[^\d]", "", regex=True)

    # Удаление всех символов, кроме букв (разных языков) и цифр
    for col in ["art_number", "engine", "number", "new_used"]:
        result_df[col] = result_df[col].str.replace(r'[^a-zA-Zа-яА-ЯёЁ\d\u4e00-\u9fff]', '', regex=True)

    # Разбиение фотографий на колонки
    photo_columns = ['main_photo_url', 'additional_photo_url_1', 'additional_photo_url_2', 'additional_photo_url_3']
    pattern = r"[;,\s]+"

    def split_photos(row):
        links = re.split(pattern, row['main_photo_url'])
        for i in range(min(len(photo_columns), len(links))):
            row[photo_columns[i]] = links[i]
        return row

    result_df = result_df.apply(split_photos, axis=1)

    # Заполнение дополнительных недостающих данных из 'product_name'
    if extract_data_from_product_name:
        def fill_missing_data(row):
            pattern = f"[{re.escape(split_symbols)}]"
            key_words = re.split(pattern, add_to_product_name.lower() + " " + row['product_name'])

            temp_arr = []
            l = len(key_words)
            for i in range(l - 1):
                temp_arr.append(key_words[i] + " " + key_words[i + 1])
            for i in range(l - 2):
                temp_arr.append(key_words[i] + " " + key_words[i + 1] + " " + key_words[i + 2])
            # Длинные слова проверяются первыми
            key_words = temp_arr[::-1] + key_words

            if row['brand'] in brand_rus_to_en:
                row['brand'] = brand_rus_to_en[row['brand']]
            if row['model'] in model_rus_to_en:
                row['model'] = model_rus_to_en[row['model']]
            car_brand = row['brand']
            car_model = row['model']
            car_engine = row['engine']
            car_body = row['body']
            car_year = row['year']

            if selected_brands:
                allowed_brands = set(selected_brands) & car_brands
            else:
                allowed_brands = car_brands


            if not car_model:
                def check_model_for_brand(brand_arg):
                    nonlocal car_model
                    if brand_arg in brand_to_models and brand_arg in allowed_brands:
                        for word in key_words:
                            if word in model_rus_to_en:
                                word = model_rus_to_en[word]
                            if word in brand_to_models[brand_arg] and len(car_model)<len(word):
                                car_model = word
                                row['model'] = word
                                row['brand'] = brand_arg

                if car_brand:
                    check_model_for_brand(car_brand)
                else:
                    for brand in allowed_brands:
                        check_model_for_brand(brand)

            if not car_brand:
                if car_model:
                    for brand in brand_to_models:
                        if car_model in brand_to_models[brand] and brand in allowed_brands:
                            car_brand = brand
                            row['brand'] = brand
                            break
                else:
                    for w in key_words:
                        if w in brand_rus_to_en:
                            w = brand_rus_to_en[w]
                        if w in allowed_brands:
                            car_brand = w
                            row['brand'] = w
                            break

            if not car_engine:
                def check_engine_for_brand(brand_arg):
                    nonlocal car_engine
                    if brand_arg in brand_to_engines and brand_arg in allowed_brands:
                        for word in key_words:
                            if word in brand_to_engines[brand_arg] and len(car_engine)<len(word):
                                car_engine = word
                                row['engine'] = word
                                row['brand'] = brand_arg

                if car_brand:
                    check_engine_for_brand(car_brand)
                else:
                    for brand in allowed_brands:
                        check_engine_for_brand(brand)

            if car_model and not car_body:
                if car_model in model_to_body_to_year:
                    for word in key_words:
                        if word in model_to_body_to_year[car_model] and len(car_body)<len(word):
                            car_body = word
                            row['body'] = word

            if not car_year:
                for match in re.findall(r'\d{4}', row['product_name']):
                    year = int(match)
                    if 1900 <= year <= 2100:
                        car_year = str(year)
                        row['year'] = str(year)
                        break
            if car_model and car_body and not car_year:
                bodies = model_to_body_to_year.get(car_model)
                if bodies:
                    year = bodies.get(car_body)
                    if year:
                        row['year'] = str(year)

            return row

        # Применяем функцию
        result_df = result_df.apply(fill_missing_data, axis=1)

    # Удаление лишних символов из engine
    result_df['engine'] = result_df['engine'].str.replace(r"[\s-]", "", regex=True)

    # Удаление дубликатов
    result_df = result_df.drop_duplicates()

    return result_df
