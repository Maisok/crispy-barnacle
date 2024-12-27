const mysql = require('mysql2');

// Настройка подключения к базе данных
const connection = mysql.createConnection({
    host: '172.19.0.4', // Хост базы данных
    user: 'root', // Имя пользователя
    password: 'wT8gn!RpC2p/z.M5', // Пароль
    database: 'where_parts_db', // Имя базы данных
});

// Функция для проверки объявлений
async function checkAdverts() {
    try {
        // SQL-запрос для выборки объявлений с status_pay = 'not_pay'
        const query = `
            SELECT id, user_id
            FROM adverts
            WHERE status_pay = 'not_pay'
        `;

        // Выполняем запрос
        const [results] = await connection.promise().query(query);

        const currentTime = new Date().toISOString(); // Получаем текущее время в формате ISO 8601

        // Проверяем каждое объявление
        for (const row of results) {
            await checkUserTariff(row.user_id, row.id, currentTime);
        }

        // Выводим сообщение о завершении функции
        console.log("The Pay function is completed");
    } catch (error) {
        console.error('Ошибка при выполнении запроса:', error.message);
    }
}

// Функция для проверки активного тарифа пользователя
async function checkUserTariff(userId, advertId, currentTime) {
    try {
        const query = `
            SELECT status, price_day_one_advert
            FROM tariffs
            WHERE id_user = ?
        `;

        // Выполняем запрос
        const [results] = await connection.promise().query(query, [userId]);

        if (results.length > 0) {
            const tariffStatus = results[0].status;
            const price = results[0].price_day_one_advert;

            if (tariffStatus === 'old') {
                // У пользователя есть активный тариф
                await processPayment(userId, advertId, price, currentTime);
            } else if (tariffStatus === 'new') {
                // У пользователя тариф со статусом 'new', списание не происходит
                await updateAdvertStatus(advertId, currentTime);
            } else {
                // Обновляем статус объявления на 'inactive'
                const updateAdvertQuery = `
                    UPDATE adverts
                    SET status_pay = 'not_pay', status_ad = 'not_activ'
                    WHERE id = ?
                `;

                await connection.promise().query(updateAdvertQuery, [advertId]);
            }
        } else {
            // У пользователя нет тарифа, обновляем статус объявления на 'inactive'
            const updateAdvertQuery = `
                UPDATE adverts
                SET status_pay = 'not_pay', status_ad = 'not_activ'
                WHERE id = ?
            `;

            await connection.promise().query(updateAdvertQuery, [advertId]);
        }
    } catch (error) {
        console.error('Ошибка при проверке тарифа:', error.message);
    }
}

// Функция для обновления статуса объявления
async function updateAdvertStatus(advertId, currentTime) {
    try {
        // Преобразуем время в формат YYYY-MM-DD HH:MM:SS
        const formattedTime = new Date(currentTime).toISOString().slice(0, 19).replace('T', ' ');

        // Обновляем статус объявления
        const updateAdvertQuery = `
            UPDATE adverts
            SET status_pay = 'pay', status_ad = 'activ', time_last_pay = ?
            WHERE id = ?
        `;

        await connection.promise().query(updateAdvertQuery, [formattedTime, advertId]);
    } catch (error) {
        console.error('Ошибка при обновлении статуса объявления:', error.message);
    }
}

// Функция для обработки оплаты
async function processPayment(userId, advertId, price, currentTime) {
    try {
        // Преобразуем время в формат YYYY-MM-DD HH:MM:SS
        const formattedTime = new Date(currentTime).toISOString().slice(0, 19).replace('T', ' ');

        // Проверяем баланс пользователя
        const balanceQuery = `
            SELECT balance
            FROM users
            WHERE id = ?
        `;

        const [results] = await connection.promise().query(balanceQuery, [userId]);
        const balance = results[0].balance;

        if (balance !== null && balance > 0) {
            // Списываем оплату с баланса
            const newBalance = balance - price;
            const updateBalanceQuery = `
                UPDATE users
                SET balance = ?
                WHERE id = ?
            `;

            await connection.promise().query(updateBalanceQuery, [newBalance, userId]);

            // Обновляем статус объявления
            const updateAdvertQuery = `
                UPDATE adverts
                SET status_pay = 'pay', status_ad = 'activ', time_last_pay = ?
                WHERE id = ?
            `;

            await connection.promise().query(updateAdvertQuery, [formattedTime, advertId]);
        }
    } catch (error) {
        console.error('Ошибка при обработке оплаты:', error.message);
    }
}

// Подключаемся к базе данных
connection.connect((err) => {
    if (err) {
        console.error('Ошибка подключения к базе данных:', err.message);
    } else {
        console.log('Подключение к базе данных успешно установлено');

        // Выполняем проверку объявлений каждую минуту
        setInterval(checkAdverts, 10000);
    }
});