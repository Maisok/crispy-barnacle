# Python converter

## Инструкция по настройке


### Создайте виртуальное окружение
Выполните следующую команду для создания виртуального окружения:
```bash
python -m venv venv
```
Где `venv` — это имя директории, куда будет установлено окружение. Вы можете использовать любое имя.

### Активируйте виртуальное окружение
- **Для Windows**:
  ```bash
  cd venv/Scripts/
  activate
  cd ../..
  ```
- **Для macOS/Linux**:
  ```bash
  source venv/bin/activate
  ```

### Установите зависимости
После активации окружения установите зависимости из файла `requirements.txt`:
```bash
pip install -r requirements.txt
```

### Проверка
Убедитесь, что зависимости установлены корректно. Например, выполните:
```bash
pip list
```
И проверьте, что все пакеты из `requirements.txt` установлены.

## Дополнительные команды

### Деактивация виртуального окружения
Чтобы выйти из виртуального окружения, выполните:
```bash
deactivate
```

### Обновление зависимостей
Если требуется добавить новые библиотеки, установите их через `pip` и обновите файл `requirements.txt`:
```bash
pip install new-package
pip freeze > requirements.txt
```

### Удаление виртуального окружения
Чтобы удалить виртуальное окружение, достаточно удалить директорию `venv`:
```bash
rm -rf venv
```

## Инструкция по запуску в docker
### Устанавливаем пакеты
```bash
sudo apt update && sudo apt install git docker.io -y
```
### Добавляем себя в группу 'docker', чтобы не использовать **sudo** (требуется перезагрузка)
```bash
sudo usermod -aG docker $USER
```
### Переходим в текущую папку
```bash
cd python_converter
```
### Создаём образ docker
```bash
docker build -t python_converter .
```
### Запуск docker контейнера на порту 8002
```bash
docker run -p 8002:8002 -ti -d python_converter
```
### После чего FastAPI должен быть доступен по адресу *http://127.0.0.1:8002/*
