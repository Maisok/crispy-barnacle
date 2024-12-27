const mysql = require('mysql2');

// Настройка подключения к базе данных
const connection = mysql.createConnection({
    host: '172.19.0.4', // Хост базы данных
    user: 'root', // Имя пользователя
    password: 'wT8gn!RpC2p/z.M5', // Пароль
    database: 'where_parts_db', // Имя базы данных
});

// Функция для обновления статусов объявлений
function updateAdverts() {
    const query = `
        UPDATE adverts
        SET status_ad = 'not_activ', status_pay = 'not_pay'
        WHERE status_ad = 'activ' AND time_last_pay <= NOW() - INTERVAL 1 HOUR
    `;

    // Выполняем запрос
    connection.query(query, (error, results) => {
        if (error) {
            console.error('Ошибка при обновлении объявлений:', error.message);
        } else {
            console.log(`Обновление статусов объявлений завершено. Обновлено строк: ${results.affectedRows}`);
        }
    });
}

// Подключаемся к базе данных
connection.connect((err) => {
    if (err) {
        console.error('Ошибка подключения к базе данных:', err.message);
    } else {
        console.log('Подключение к базе данных успешно установлено');

        // Выполняем обновление объявлений каждую минуту
        setInterval(updateAdverts, 10000); // 60000 миллисекунд = 1 минута
    }
});