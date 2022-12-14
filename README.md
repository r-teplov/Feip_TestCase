Предположим, что перед нами стоит задача обработать ответ от API или HTTP запрос, содержащий данные в формате JSON.

Нужно написать библиотеку "санитайзер", которая занимается валидацией и нормализацией данных в соответствии с переданной спецификацией.


#### Требования:
1. Самостоятельное выполнение задания без оглядки на существующие решения
2. Язык PHP 7.1+/Python/JavaScript/TypeScript без сторонних библиотек (кроме библиотек для тестирования)
3. Поддержка следующих типов данных:
    * Строка
    * Целое число
    * Число с плавающей точкой
    * Российский федеральный номер телефона
    * Структура (ассоциативный массив с заранее известными ключами)
    * Массив из однотипных элементов
4. Значения элементов в структурах и массивах могут быть любого из поддерживаемых типов
5. Возможность расширения путём добавления поддержки новых типов
6. Генерация списка всех ошибок для некорректных значений. Формат описания ошибок должен предоставлять возможность сопоставить каждую ошибку с исходным значением. Например, если входные данные были сгенерированы на основе HTML-формы с вложенными (табличными) полями, должно быть технически возможно сопоставить каждую ошибку конкретному полю формы
7. Тесты


#### Примеры:
1. из JSON '{"foo": "123", "bar": "asd", "baz": "8 (950) 288-56-23"}' при указанных программистом типах полей "целое число", "строка" и "номер телефона" соответственно должен получиться ассоциативный массив с тремя полями: целочисленным foo = 123, строковым bar = "asd" и строковым "baz" = "79502885623"
2. при указании для строки "123абв" типа "целое число" должна быть сгенерирована ошибка
3. при указании для строки "260557" типа "номер телефона" должна быть сгенерирована ошибка