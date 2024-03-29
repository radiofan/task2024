# Тестовое задание для вакансии "бэкэнд-разработчик", МИП ЦКИ, 2024

Нужно разработать и реализовать API для датчиков и SPA-приложения.
Приложение позволяет получать данные мониторинга по нескольким параметрам и просматривать историю изменений этих параметров. Параметры мониторинга: температура (в градусах Цельсия), давление (в мегапаскалях), скорость вращения (в оборотах в минуту).

![архитектура](resources/backend2024.svg)

Данные от датчиков приходят по HTTP в строках вида `"<параметр>=<значение>"`, один запрос на единицу данных, (примеры: `T=20`, `P=304.5`, `v=3053`). Датчикам можно задавать URL на который отправляются запросы.

Приложение будет показывать график изменения параметра за определённый интервал времени, API должно давать такую возможность.

 - Решение должно быть выполнено на фреймворке Laravel;
 - Решение должно сопровождаться README-файлом, достаточным для передачи проекта другому разработчику на доработку и девопсу для организации эксплуатации в боевом окружении;
 - Код решения должен быть размещено на любом публично-доступном git-хостинге.
