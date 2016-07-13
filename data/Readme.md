### Doctrine orm:schema commands

Вывод информации

    ./vendor/bin/doctrine-module orm:info

Валидация схемы

    ./vendor/bin/doctrine-module orm:validate-schema

Обновление схемы

    ./vendor/bin/doctrine-module orm:schema-tool:update --force

Конвертация схемы из базы данных

    ./vendor/bin/doctrine-module orm:convert-mapping --namespace="Modulename\\Entity\\" --force  --from-database annotation ./module/Modulename/src/

Создание сущностей

    ./vendor/bin/doctrine-module orm:generate-entities ./module/Modulename/src/ --generate-annotations=true

Очистка базы данных

    ./vendor/bin/doctrine-module orm:schema-tool:drop --force

### SQL файлы

* файлы dump_*.sql.zip - дамп бд за * число.
* файлы sql_*.sql.zip - интекции, которые необходимо накатить на последний дамп.