document.addEventListener('DOMContentLoaded', function() {
    // Глобальная переменная для отслеживания режима удаления нескольких товаров
    let isMultipleDeleteMode = false;

    // Функция для открытия модального окна редактирования
    function openEditModal(id) {
        var row = document.querySelector(`tr[data-id-info="${id}"]`);

        document.getElementById("editAdvertId").value = id;
        document.getElementById("art_number").value = row.getAttribute('data-art-number');
        document.getElementById("product_name").value = row.getAttribute('data-product-name');
        document.getElementById("number").value = row.getAttribute('data-number-info');
        document.getElementById("new_used").value = row.getAttribute('data-new-used');
        document.getElementById("brand").value = row.getAttribute('data-brand-info');
        document.getElementById("model").value = row.getAttribute('data-model-info');
        document.getElementById("year").value = row.getAttribute('data-year');
        document.getElementById("body").value = row.getAttribute('data-body-info');
        document.getElementById("engine").value = row.getAttribute('data-engine-info');
        document.getElementById("L_R").value = row.getAttribute('data-L_R');
        document.getElementById("F_R").value = row.getAttribute('data-F_R');
        document.getElementById("U_D").value = row.getAttribute('data-U_D');
        document.getElementById("color").value = row.getAttribute('data-color');
        document.getElementById("applicability").value = row.getAttribute('data-applicability');
        document.getElementById("quantity").value = row.getAttribute('data-quantity');
        document.getElementById("price").value = row.getAttribute('data-price-info');
        document.getElementById("availability").value = row.getAttribute('data-availability');
        document.getElementById("main_photo_url").value = row.getAttribute('data-main-photo-url');
        document.getElementById("additional_photo_url_1").value = row.getAttribute('data-additional-photo-url-1');
        document.getElementById("additional_photo_url_2").value = row.getAttribute('data-additional-photo-url-2');
        document.getElementById("additional_photo_url_3").value = row.getAttribute('data-additional-photo-url-3');

        // Заполнение скрытых полей старыми значениями
        document.getElementById("old_art_number").value = row.getAttribute('data-art-number');
        document.getElementById("old_product_name").value = row.getAttribute('data-product-name');
        document.getElementById("old_number").value = row.getAttribute('data-number-info');
        document.getElementById("old_new_used").value = row.getAttribute('data-new-used');
        document.getElementById("old_brand").value = row.getAttribute('data-brand-info');
        document.getElementById("old_model").value = row.getAttribute('data-model-info');
        document.getElementById("old_year").value = row.getAttribute('data-year');
        document.getElementById("old_body").value = row.getAttribute('data-body-info');
        document.getElementById("old_engine").value = row.getAttribute('data-engine-info');
        document.getElementById("old_L_R").value = row.getAttribute('data-L_R');
        document.getElementById("old_F_R").value = row.getAttribute('data-F_R');
        document.getElementById("old_U_D").value = row.getAttribute('data-U_D');
        document.getElementById("old_color").value = row.getAttribute('data-color');
        document.getElementById("old_applicability").value = row.getAttribute('data-applicability');
        document.getElementById("old_quantity").value = row.getAttribute('data-quantity');
        document.getElementById("old_price").value = row.getAttribute('data-price-info');
        document.getElementById("old_availability").value = row.getAttribute('data-availability');
        document.getElementById("old_main_photo_url").value = row.getAttribute('data-main-photo-url');
        document.getElementById("old_additional_photo_url_1").value = row.getAttribute('data-additional-photo-url-1');
        document.getElementById("old_additional_photo_url_2").value = row.getAttribute('data-additional-photo-url-2');
        document.getElementById("old_additional_photo_url_3").value = row.getAttribute('data-additional-photo-url-3');

        // Показываем модальное окно
        var editModal = document.getElementById("editModal");
        editModal.style.display = "block";
        document.body.classList.add('modal-open'); // Блокируем скролл страницы

        // Закрытие модального окна при нажатии на элемент "close"
        var editSpan = editModal.getElementsByClassName("close")[0];
        editSpan.onclick = function() {
            editModal.style.display = "none";
            document.body.classList.remove('modal-open'); // Разблокируем скролл страницы
        }

        // Закрытие модального окно при клике вне его области
        editModal.onclick = function(event) {
            if (event.target == editModal) {
                editModal.style.display = "none";
                document.body.classList.remove('modal-open'); // Разблокируем скролл страницы
            }
        }
    }

    // Получаем все кнопки редактирования
    var editButtons = document.getElementsByClassName("edit-btn");

    // Перебираем все кнопки редактирования и добавляем обработчик события для каждой кнопки
    for (var i = 0; i < editButtons.length; i++) {
        editButtons[i].addEventListener("click", function() {
            var id = this.getAttribute('data-id');
            openEditModal(id);
        });
    }

    // Функция для открытия модального окна просмотра
    function openViewModal(event, id) {
        // Проверяем, был ли клик на элементе внутри столбца "Действия"
        if (event.target.closest('td:last-child')) {
            return; // Если да, то ничего не делаем
        }

        // Проверяем, активен ли режим удаления нескольких товаров
        if (isMultipleDeleteMode) {
            console.log('Режим удаления нескольких товаров активен, модальное окно не открывается');
            return; // Если режим активен, модальное окно не открывается
        }

        var row = document.querySelector(`tr[data-id-info="${id}"]`);

        var artNumber = row.getAttribute('data-art-number');
        var productName = row.getAttribute('data-product-name');
        var brandInfo = row.getAttribute('data-brand-info');
        var modelInfo = row.getAttribute('data-model-info');
        var bodyInfo = row.getAttribute('data-body-info');
        var numberInfo = row.getAttribute('data-number-info');
        var engineInfo = row.getAttribute('data-engine-info');
        var mainPhotoUrl = row.getAttribute('data-main-photo-url') || '/static/not_found.jpg'; // Путь к изображению "Image Not Found"
        var additionalPhotoUrl1 = row.getAttribute('data-additional-photo-url-1') || '/static/not_found.jpg';
        var additionalPhotoUrl2 = row.getAttribute('data-additional-photo-url-2') || '/static/not_found.jpg';
        var additionalPhotoUrl3 = row.getAttribute('data-additional-photo-url-3') || '/static/not_found.jpg';
        var priceInfo = row.getAttribute('data-price-info');

        // Заполняем модальное окно данными товара
        document.getElementById("modalMainImg").src = mainPhotoUrl;
        document.getElementById("modalId").textContent = id;
        document.getElementById("modalProductName").textContent = productName;
        document.getElementById("modalBrand").textContent = brandInfo;
        document.getElementById("modalModel").textContent = modelInfo;
        document.getElementById("modalBody").textContent = bodyInfo;
        document.getElementById("modalEngine").textContent = engineInfo;
        document.getElementById("modalNumber").textContent = numberInfo;
        document.getElementById("modalPrice").textContent = priceInfo;

        // Очищаем контейнер для дополнительных фотографий
        var additionalImagesContainer = document.getElementById("additionalImagesContainer");
        additionalImagesContainer.innerHTML = '';

        // Добавляем дополнительные фотографии, если они есть
        var additionalPhotos = [additionalPhotoUrl1, additionalPhotoUrl2, additionalPhotoUrl3];
        additionalPhotos.forEach(function(photoUrl, index) {
            if (photoUrl !== '/static/not_found.jpg') {
                var imgContainer = document.createElement('div');
                imgContainer.className = 'w-16 h-16 bg-gray-200 rounded-lg flex items-center justify-center';
                var img = document.createElement('img');
                img.id = 'modalAdditionalImg' + (index + 1);
                img.src = photoUrl;
                img.alt = 'Дополнительное фото ' + (index + 1);
                img.className = 'w-10 h-10 cursor-pointer';
                imgContainer.appendChild(img);
                additionalImagesContainer.appendChild(imgContainer);
            }
        });

        // Показываем модальное окно
        var viewModal = document.getElementById("viewModal");
        viewModal.style.display = "flex";
        document.body.classList.add('modal-open'); // Блокируем скролл страницы

        // Закрытие модального окна при нажатии на элемент "close"
        var viewSpan = viewModal.getElementsByClassName("close")[0];
        viewSpan.onclick = function() {
            viewModal.style.display = "none";
            document.body.classList.remove('modal-open'); // Разблокируем скролл страницы
        }

        // Закрытие модального окна при клике вне его области
        viewModal.onclick = function(event) {
            if (event.target == viewModal) {
                viewModal.style.display = "none";
                document.body.classList.remove('modal-open'); // Разблокируем скролл страницы
            }
        }

        // Обработка клика на основное фото
        document.getElementById("modalMainImg").onclick = function() {
            var mainImgSrc = this.src;
            var additionalImgSrc = additionalImagesContainer.firstChild.firstChild.src;
            this.src = additionalImgSrc;
            additionalImagesContainer.firstChild.firstChild.src = mainImgSrc;
        };

        // Обработка клика на дополнительные фотографии
        var additionalImages = additionalImagesContainer.querySelectorAll("img");
        additionalImages.forEach(function(img) {
            img.onclick = function() {
                var mainImgSrc = document.getElementById("modalMainImg").src;
                var additionalImgSrc = this.src;
                document.getElementById("modalMainImg").src = additionalImgSrc;
                this.src = mainImgSrc;
            };
        });
    }

    // Получаем все строки таблицы
    var tableRows = document.getElementsByTagName("tr");

    // Перебираем все строки таблицы и добавляем обработчик события для каждой строки
    for (var i = 1; i < tableRows.length; i++) { // Начинаем с 1, чтобы пропустить заголовок
        tableRows[i].addEventListener("click", function(event) {
            var idNumber = this.getAttribute('data-id-info');
            openViewModal(event, idNumber);
        });
    }

    // Обработка добавления в корзину
    var addToCartBtn = document.getElementById('addToCartBtn');
    var cartNotification = document.getElementById('cartNotification');

    addToCartBtn.addEventListener('click', function() {
        cartNotification.textContent = 'Товар добавлен в корзину!';
        setTimeout(() => {
            cartNotification.textContent = '';
        }, 3000);
    });

    // Обработчик для кнопки "Удалить несколько"
    document.getElementById('deleteMultipleBtn').addEventListener('click', function() {
        isMultipleDeleteMode = !isMultipleDeleteMode; // Переключаем режим
        console.log('Режим удаления нескольких товаров:', isMultipleDeleteMode ? 'активен' : 'не активен');
    });
});