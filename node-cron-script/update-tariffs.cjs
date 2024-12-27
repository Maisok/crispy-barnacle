const mysql = require('mysql2');

// Настройка подключения к базе данных
const connection = mysql.createConnection({
    host: '172.19.0.4', // Хост базы данных
    user: 'root', // Имя пользователя
    password: 'wT8gn!RpC2p/z.M5', // Пароль
    database: 'where_parts_db', // Имя базы данных
});

// Функция для обновления статуса тарифов
function updateTariffs() {
    const query = `
        UPDATE tariffs
        SET status = 'old'
        WHERE status = 'new' AND created_at <= NOW() - INTERVAL 14 DAY
    `;

    // Выполняем запрос
    connection.query(query, (error, results) => {
        if (error) {
            console.error('Ошибка при обновлении тарифов:', error.message);
        } else {
            console.log(`Обновление статуса тарифов завершено. Обновлено строк: ${results.affectedRows}`);

            // Если ни одна строка не обновлена, выводим сообщение
            if (results.affectedRows === 0) {
                console.log('Нет тарифов, которые нужно обновить.');
            }
        }
    });
}

// Функция для проверки данных в таблице tariffs
function checkTariffs() {
    const query = `
        SELECT id_tariff, status, created_at
        FROM tariffs
        WHERE status = 'new' AND created_at <= NOW() - INTERVAL 14 DAY
    `;

    // Выполняем запрос
    connection.query(query, (error, results) => {
        if (error) {
            console.error('Ошибка при проверке тарифов:', error.message);
        } else {
            console.log(`Найдено тарифов для обновления: ${results.length}`);

            // Выводим данные для отладки
            if (results.length > 0) {
                console.log('Данные тарифов для обновления:');
                results.forEach((row) => {
                    console.log(`ID: ${row.id_tariff}, Статус: ${row.status}, Создан: ${row.created_at}`);
                });
            } else {
                console.log('Нет тарифов, которые нужно обновить.');
            }
        }
    });
}

// Подключаемся к базе данных
connection.connect((err) => {
    if (err) {
        console.error('Ошибка подключения к базе данных:', err.message);
    } else {
        console.log('Подключение к базе данных успешно установлено');

        // Проверяем данные в таблице tariffs
        checkTariffs();

        // Выполняем обновление тарифов каждую минуту
        setInterval(updateTariffs, 10000); // 60000 миллисекунд = 1 минута
    }
});