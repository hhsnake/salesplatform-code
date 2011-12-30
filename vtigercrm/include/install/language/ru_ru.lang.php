<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: vtiger CRM Open Source
 * All Rights Reserved.
 * Description: Defines the Russian language pack.
 * The Initial Translator is Eugene Babiy (eugene.babiy@gmail.com).
 * This Language Pack modified and supported by SalesPlatform Ltd
 * SalesPlatform vtiger CRM Russian Community: http://community.salesplatform.ru/
 * If you have any questions or comments, please email: devel@salesplatform.ru
 ************************************************************************************/

$optionalModuleStrings = array(
	'CustomerPortal_description'=>'Интерфейс управления поведением Плагина Клиентского Портала',
	'FieldFormulas_description'=>'Настройка правил для пользовательских полей для обновления значения после сохранения записи',
	'RecycleBin_description'=>'Модуль для управления удаленными записями, предоставляет возможность восстановить или полностью удалить запись',
	'Tooltip_description'=>'Настройка комментариев к полям, которые могут быть комбинацией других полей',
	'Webforms_description'=>'Функция со стороны сервера для построения клиентских веб-форм для получения данных с сайта',
	'SMSNotifier_description'=>'Отправляет SMS-сообщения контрагентам, контактам и потенциальным клиентам',
	'Assets_description'=>'Активы представляют владение собственностью которая может быть превращена в прибыль',
	'ModComments_description' => 'Возможность добавления комментариев к любому из относящихся модулей',
	'Projects_description' => 'Добавляет возможность Управления Проектами',
);

$installationStrings = array(
	'LBL_VTIGER_CRM_5' => 'SalesPlatform VtigerCRM 5.2.1',
	'LBL_CONFIG_WIZARD' => 'Мастер Настройки',
	'LBL_WELCOME' => 'Добро пожаловать',
	'LBL_WELCOME_CONFIG_WIZARD' => 'Добро пожаловать в Мастер Настройки',
	'LBL_ABOUT_CONFIG_WIZARD' => 'Этот мастер настройки поможет вам установить SalesPlatform VtigerCRM ',
	'LBL_ABOUT_VTIGER' => 'Vtiger CRM - это свободный проект CRM-системы с полностью открытым исходным кодом. <br><br>
				Цель проекта - предоставить полноценное и качественное CRM-решение с низкой общей стоимостью владения для малых и средних предприятий.<br><br> 
				Vtiger CRM имеет активное сообщество разработчиков и пользователей во многих странах.<br> <br>
				Устанавливаемый Вами дистрибутив создан при поддержке сообщества SalesPlatform.ru. Этот дистрибутив приближен к потребностям пользователей в Российской Федерации,  
				содержит обновления основного дистрибутива, улучшения и исправления ошибок.<br><br>
				Вы всегда можете обратиться к разработчикам SalesPlatform.ru за помощью в улучшении данного дистрибутива.',
	'LBL_INSTALL' => 'Установить',
	'LBL_MIGRATE' => 'Обновить',
	'ERR_RESTRICTED_FILE_ACCESS' => 'Извините! Попытка доступа к запрещенному файлу',
	'LBL_INSTALLATION_CHECK' => 'Проверка Установки',
	'LBL_BACK' => 'Назад',
	'LBL_NEXT' => 'Вперед',
	'LBL_AGREE' => 'Согласен',
	'LBL_SYSTEM_CONFIGURATION'=> 'Конфигурация Системы',
	'LBL_INSTALLATION_CHECK' => 'Проверка Установки',
	'LBL_PRE_INSTALLATION_CHECK' => 'Проверка перед Установкой',
	'LBL_CHECK_AGAIN' => 'Проверить',
	'LBL_CONFIRM_SETTINGS' => 'Подтвердить Параметры',
	'LBL_CONFIRM_CONFIG_SETTINGS' => 'Подтвердить Параметры Конфигурации',
	'LBL_CONFIG_FILE_CREATION' => 'Создание Конфигурационного Файла',
	'LBL_OPTIONAL_MODULES' => 'Дополнительные Модули',
	'LBL_SELECT_OPTIONAL_MODULES_TO_install' => 'Выберите дополнительные модули для установки',
	'LBL_SELECT_OPTIONAL_MODULES_TO_update' => 'Выберите дополнительные модули для обновления',
	'LBL_SELECT_OPTIONAL_MODULES_TO_copy' => 'Выберите дополнительные модули для копирования',
	'MSG_CONFIG_FILE_CREATED' => 'Файл конфигурации (config.inc.php) был успешно создан',
	'LBL_FINISH' => 'Готово',
	'LBL_CONFIG_COMPLETED' => 'Настройка Завершена',
	'LBL_PHP_VERSION_GT_5' => 'PHP version >= 5.2',
	'LBL_YES' => 'Да',
	'LBL_NO' => 'Нет',
	'LBL_NOT_CONFIGURED' => 'Не настроено',
	'LBL_IMAP_SUPPORT' => 'Поддержка IMAP',
	'LBL_ZLIB_SUPPORT' => 'Поддержка Zlib',
	'LBL_GD_LIBRARY' => 'Графическая библиотека GD',
	'LBL_RECOMMENDED_PHP_SETTINGS' => 'Рекомендованные параметры PHP',
	'LBL_DIRECTIVE' => 'Параметр',
	'LBL_RECOMMENDED' => 'Рекомендовано',
	'LBL_PHP_INI_VALUE' => 'значение в PHP.ini',
	'LBL_READ_WRITE_ACCESS' => 'Доступ на Чтение/Запись',
	'LBL_NOT_RECOMMENDED' => 'Не рекомендуется',
	'LBL_PHP_DIRECTIVES_HAVE_RECOMMENDED_VALUES' => 'Ваши параметры PHP имеют Рекомендованные значения',
	'MSG_PROVIDE_READ_WRITE_ACCESS_TO_PROCEED' => 'Предоставьте права на Чтение/Запись к файлам и папкам в списке',
	'WARNING_PHP_DIRECTIVES_NOT_RECOMMENDED_STILL_WANT_TO_PROCEED' => 'Некоторые Параметры PHP не соответствуют рекомендованным значениям. Это может повлиять на некоторые из функций Vtiger CRM. Вы уверены что хотите продолжить?',
	'LBL_CHANGE' => 'Изменить',
	'LBL_DATABASE_INFORMATION' => 'Информация БД',
	'LBL_CRM_CONFIGURATION' => 'Конфигурация CRM',
	'LBL_USER_CONFIGURATION' => 'Параметры Пользователя',
	'LBL_DATABASE_TYPE' => 'Тип БД',
	'LBL_NO_DATABASE_SUPPORT' => 'Отсутствует поддержка БД',
	'LBL_HOST_NAME' => 'Хост',
	'LBL_USER_NAME' => 'Пользователь',
	'LBL_PASSWORD' => 'Пароль',
	'LBL_DATABASE_NAME' => 'Название БД',
	'LBL_CREATE_DATABASE' => 'Создать БД',
	'LBL_DROP_IF_EXISTS' => 'Удалит старую, если она существует',
	'LBL_ROOT' => 'Root',
	'LBL_UTF8_SUPPORT' => 'Поддержка UTF-8',
	'LBL_URL' => 'URL',
	'LBL_CURRENCY_NAME' => 'Валюта',
	'LBL_USERNAME' => 'Пользователь',
	'LBL_EMAIL' => 'Email',
	'LBL_POPULATE_DEMO_DATA' => 'Наполнить базу данных демонстрационными данными',
	'LBL_DATABASE' => 'База данных',
	'LBL_SITE_URL' => 'Адрес сайта',
	'LBL_PATH' => 'Путь',
	'LBL_MISSING_REQUIRED_FIELDS' => 'Отсутствуют обязательные поля',
	'ERR_ADMIN_EMAIL_INVALID' => 'Адрес e-mail администратора с ошибкой',
	'ERR_STANDARDUSER_EMAIL_INVALID' => 'Адрес e-mail стандартного пользователя с ошибкой',
	'WARNING_LOCALHOST_IN_SITE_URL' => 'Укажите конкретное наименование хоста вместо \"localhost\" в поле Адрес Сайта, иначе у вас могут возникнуть некоторые трудности с работой плагинов. Вы желаете Продолжить?',
	'LBL_DATABASE_CONFIGURATION' => 'Параметры БД',
	'LBL_ENABLED' => 'Включено',
	'LBL_NOT_ENABLED' => 'Отключено',
	'LBL_SITE_CONFIGURATION' => 'Конфигурация сайта',
	'LBL_DEFAULT_CHARSET' => 'Кодировка по умолчанию',
	'ERR_DATABASE_CONNECTION_FAILED' => 'Невозможно соединится с Сервером БД',
	'ERR_INVALID_MYSQL_PARAMETERS' => 'Указаны неверные параметры соединения с сервером MySQL',
	'MSG_LIST_REASONS' => 'Причины этого могут быть следующие',
	'MSG_DB_PARAMETERS_INVALID' => 'пользователь БД, пароль, хост, тип БД или порт указаны неверно',
	'MSG_DB_USER_NOT_AUTHORIZED' => 'указанный пользователь БД не имеет прав доступа к серверу БД с данного хоста',
	'LBL_MORE_INFORMATION' => 'Дополнительная Информация',
	'ERR_INVALID_MYSQL_VERSION' => 'Версия MySQL не поддерживается, пожалуйста используйте версию MySQL 4.1.x или выше',
	'ERR_UNABLE_CREATE_DATABASE' => 'Невозможно создать БД',
	'MSG_DB_ROOT_USER_NOT_AUTHORIZED' => 'Сообщение: Указанный администратор БД не имеет прав на созание БД или название БД содержит специальные символы. Попробуйте изменить настройки БД',
	'ERR_DB_NOT_FOUND' => 'Эта БД не найдена. Попробуйте изменить настройки БД',
	'LBL_SUCCESSFULLY_INSTALLED' => 'Установка завершена успешно',
	'LBL_DEMO_DATA_IN_PROGRESS' => 'Внесение демо-данных в процессе',
	'LBL_PLEASE_WAIT' => 'Пожалуйста, ждите',
	'LBL_ALL_SET_TO_GO' => 'готова к работе!',
	'LBL_INSTALL_PHP_FILE_RENAMED' => 'Ваш файл install.php был переименован в',
	'LBL_MIGRATE_PHP_FILE_RENAMED' => 'Ваш файл migrate.php был переименован в',
	'LBL_INSTALL_DIRECTORY_RENAMED' => 'Ваша папка install была переименована в',
	'WARNING_RENAME_INSTALL_PHP_FILE' => 'Мы рекомендуем вам переименовать файл install.php',
	'WARNING_RENAME_MIGRATE_PHP_FILE' => 'Мы рекомендуем вам переименовать файл migrate.php',
	'WARNING_RENAME_INSTALL_DIRECTORY' => 'Мы рекомендуем вам переименовать папку install',
	'LBL_LOGIN_USING_ADMIN' => 'Пожалуйста, войдите в систему, используя логин "admin" и пароль, который вы ввели на шаге 3/4',
	'LBL_SET_OUTGOING_EMAIL_SERVER' => 'Не забудьте настроить сервер исходящей почты (настройки доступны в разделе Настройки-&gt;Сервер Исходящей Почты)',
	'LBL_RENAME_HTACCESS_FILE' => 'Переименуйте файл htaccess.txt на .htaccess чтобы контролировать доступ к общим файлам',
	'MSG_HTACCESS_DETAILS' => 'Файл .htaccess будет работать, если в файле настроек сервера Apache (httpd.conf) указана опция "<b>AllowOverride All</b>" для DocumentRoot или для текущего пути к vtiger.<br>
				   				Если опция AllowOverride установлена в None, т.е. "<b>AllowOverride None</b>", то .htaccess не будет использован.',
	'LBL_YOU_ARE_IMPORTANT' => 'Вы очень важны для нас!',
	'LBL_PRIDE_BEING_ASSOCIATED' => 'Мы гордимся сотрудничеством с Вами',
	'LBL_TALK_TO_US_AT_FORUMS' => 'Общайтесь с нами в <a href="http://community.salesplatform.ru/forums">форумах</a>',
	'LBL_DISCUSS_WITH_US_AT_BLOGS' => 'Читайте наши <a href="http://community.salesplatform.ru/blogs">блоги</a>',
	'LBL_WE_AIM_TO_BE_BEST' => 'Наша цель - просто быть лучшими',
	'LBL_SPACE_FOR_YOU' => 'Присоединяйтесь, для Вас тоже найдется место!',	
	'LBL_NO_OPTIONAL_MODULES_FOUND' => 'Дополнительные модули не обнаружены',
	'LBL_PREVIOUS_INSTALLATION_INFORMATION' => 'Информация предыдущей установки',
	'LBL_PREVIOUS_INSTALLATION_PATH' => 'Путь к предыдущей установке',
	'LBL_PREVIOUS_INSTALLATION_VERSION' => 'Версия предыдущей установки',
	'LBL_MIGRATION_DATABASE_NAME' => 'Наименование БД для Обновления',
	'LBL_IMPORTANT_NOTE' => 'Важное Замечание',
	'MSG_TAKE_DB_BACKUP' => 'Не забудьте сделать <b>резервную копию (dump) БД</b> перед продолжением',
	'QUESTION_MIGRATE_USING_NEW_DB' => 'Обновление с использованием новой БД',
	'MSG_CREATE_DB_WITH_UTF8_SUPPORT' => 'Создать сначала БД с поддержкой кодировки UTF8',
	'LBL_EG' => 'напр.',
	'MSG_COPY_DATA_FROM_OLD_DB' => '<b>Копировать данные (dump)</b> из предыдущей БД в новую',
	'LBL_SELECT_PREVIOUS_INSTALLATION_VERSION' => 'Пожалуйста выберите версию предыдущей установки',
	'LBL_SOURCE_CONFIGURATION' => 'Параметры Источника',
	'LBL_OLD' => 'Старое',
	'LBL_NEW' => 'Новое',
	'LBL_INNODB_ENGINE_CHECK' => 'Проверка движка InnoDB',
	'LBL_FIXED' => 'Исправлено',
	'LBL_NOT_FIXED' => 'Не исправлено',
	'LBL_NEW_INSTALLATION_PATH' => 'Путь к новой установке',
	'ERR_CANNOT_WRITE_CONFIG_FILE' => 'Невозможно записать файл конфигурации (config.inc.php ). Проверьте права доступа и перезапустите установку',
	'ERR_DATABASE_NOT_FOUND' => 'ОШИБКА : Эта БД не найдена. Укажите правильное наименование БД',
	'ERR_NO_CONFIG_FILE' => 'Указанный источник не содержит конфигурационного файла. Пожалуйста, укажите правильный источник',
	'ERR_NO_USER_PRIV_DIR' => 'Указанный источник не содержит каталога прав пользователей. Пожалуйста, укажите правильный источник',
	'ERR_NO_STORAGE_DIR' => 'Указанный источник не содержит каталога Storage. Пожалуйста, укажите правильный источник',
	'ERR_NO_SOURCE_DIR' => 'Указанный источник не найден. Пожалуйста, укажите правильный источник',
	'ERR_NOT_VALID_USER' => 'Недопустимый пользователь. Пожалуйста, введите данные администратора',
	'ERR_MIGRATION_DATABASE_IS_EMPTY' => 'Эта база данных пуста. Пожалуйста, скопируйте данные из старой БД для миграции',
	'ERR_NOT_AUTHORIZED_TO_PERFORM_THE_OPERATION' => 'Недостаточно полномочий для выполнения операции',
	'LBL_DATABASE_CHECK' => 'Проверка Базы Данных',
	'MSG_TABLES_IN_INNODB' => 'Таблицы базы данных имеют правильный тип (InnoDB)',
	'MSG_CLOSE_WINDOW_TO_PROCEED' => 'Вы можете закрыть это окно и продолжить миграцию',
	'LBL_RECOMMENDATION_FOR_PROPERLY_WORKING_CRM' => 'Для правильной работы vtiger CRM мы рекомендуем следующее',
	'LBL_TABLES_SHOULD_BE_INNODB' => 'Следующие таблицы должны иметь тип InnoDB',
	'QUESTION_WHAT_IS_INNODB' => 'Что такое InnoDB',
	'LBL_TABLES_CHARSET_TO_BE_UTF8' => 'Чтобы получить полную поддержку UTF-8, таблицы должны по умолчанию иметь кодировку UTF8',
	'LBL_FIX_ENGINE_FOR_ALL_TABLES' => 'Исправить движок для всех таблиц',
	'LBL_TABLE' => 'Таблица',
	'LBL_TYPE' => 'Тип',
	'LBL_CHARACTER_SET' => 'Кодировка',
	'LBL_CORRECT_ENGINE_TYPE' => 'Правильный тип движка',
	'LBL_FIX_NOW' => 'Исправить',
	'LBL_CLOSE' => 'Закрыть',
	'LBL_PRE_MIGRATION_TOOLS' => 'Инструменты перед обновлением',
	'ERR_TABLES_NOT_INNODB' => 'Движок ваших таблиц БД не рекомендуется "Innodb"',
	'MSG_CHANGE_ENGINE_BEFORE_MIGRATION' => 'Пожалуйста измените движок перед обновлением',
	'LBL_VIEW_REPORT' => 'Просмотр Отчета',
	'LBL_IMPORTANT' => 'Важно',
	'LBL_DATABASE_BACKUP' => 'Резервирование БД',
	'LBL_DATABASE_COPY' => 'Копирование БД',
	'LBL_DB_DUMP_DOWNLOAD' => 'Загрузка копии БД',
	'LBL_DB_COPY' => 'Копия БД',
	'QUESTION_NOT_TAKEN_BACKUP_YET' => 'Резервное копирование еще не выполнялось',
	'LBL_CLICK_FOR_DUMP_AND_SAVE' => '<b>&#171; Нажмите</b> на левую иконку чтобы начать резервное копирование и <b>Сохранить</b> полученную копию',
	'LBL_NOTE' => 'Примечание',
	'LBL_RECOMMENDED' => 'Рекомендовано',
	'MSG_PROCESS_TAKES_LONGER_TIME_BASED_ON_DB_SIZE' => 'Этот процесс может занять больше времени в зависимости от размера БД',
	'QUESTION_MIGRATING_TO_NEW_DB' => 'Вы обновляете систему на новую БД',
	'LBL_CLICK_FOR_NEW_DATABASE' => '<b>&#171; Нажмите</b> на левую иконку, если Вы не установили БД со старыми данными',
	'MSG_USE_OTHER_TOOLS_FOR_DB_COPY' => 'Используйте любую программу (mysql, phpMyAdmin) для создания новой БД с данными',
	'LBL_COPY_OLD_DB_TO_NEW_DB' => 'Скопируйте Вашу текущую БД в Новую БД, которая будет использована для миграции',
	'LBL_IF_DATABASE_EXISTS_WILL_RECREATE' => 'Если база данных существует, она будет перезаписана',
	'LBL_SHOULD_BE_PRIVILEGED_USER' => 'Требуются права на операцию CREATE DATABASE',
	'ERR_FAILED_TO_FIX_TABLE_TYPES' => 'Возникла ошибка при исправлении типа таблиц',
	'ERR_SPECIFY_NEW_DATABASE_NAME' => 'Пожалуйста, укажите название новой базы данных',
	'ERR_SPECIFY_ROOT_USER_NAME' => 'Пожалуйста, укажите логин администратора',
	'ERR_DATABASE_COPY_FAILED' => '<span class="redColor">Ошибка</span> создания копии БД, пожалуйста, сделайте это вручную',
	'MSG_DATABASE_COPY_SUCCEDED' => 'Копия базы данных успешно создана.<br />Нажмите Далее &#187; для продолжения',
	'MSG_SUCCESSFULLY_FIXED_TABLE_TYPES' => 'Таблицы успешно переведены на движок InnoDB',
	'LBL_MIGRATION' => 'Обновление',
	'LBL_SOURCE_VERSION_NOT_SET' => 'Не указана версия источника. Проверьте vtigerversion.php и продолжайте процесс обновления',
	'LBL_GOING_TO_APPLY_DB_CHANGES' => 'Сейчас изменения будут записаны в базу данных',
	'LBL_DATABASE_CHANGES' => 'Изменения БД',
	'LBL_STARTS' => 'Начинается',
	'LBL_ENDS' => 'Заканчивается',
	'LBL_SUCCESS' => 'УСПЕШНО',
	'LBL_FAILURE' => 'ОШИБКА',
	'LBL_MIGRATION_FINISHED' => 'Обновление установлено',
	'LBL_OLD_VERSION_IS_AT' => 'Ваша старая система доступна по ссылке: ',
	'LBL_CURRENT_SOURCE_PATH_IS' => 'Ваша обновленная система доступна по адресу: ',
	'LBL_DATABASE_EXTENSION' =>'Дополнение БД',
	'LBL_DOCUMENTATION_TEXT' => 'Документацию, включая Инструкцию Пользователя, вы можете найти на',
	'LBL_USER_PASSWORD_CHANGE_NOTE' => 'пароль каждого пользователя будет изменен на его логин. Предупредите пользователей чтобы изменили пароли',
	'LBL_PASSWORD_FIELD_CHANGE_FAILURE' => "изменение пароля пользователя не удалось",
	'LBL_OPENSSL_SUPPORT' => 'Поддержка OpenSSL',
	'LBL_OPTIONAL_MORE_LANGUAGE_PACK' => 'Дополнительные модули переводов доступны на',
	'LBL_GETTING_STARTED' => 'Начало работы:',
	'LBL_GETTING_STARTED_TEXT' => 'Теперь вы можете начать пользоватся вашей CRM-системой.',
	'LBL_YOUR_LOGIN_PAGE' => 'Страница входа:',
	'LBL_ADD_USERS' => 'Чтобы добавить больше пользователей, откройте страницу Настройки',
	'LBL_SETUP_BACKUP' => "Установите 'Сервер Резервного Копирования', чтобы данные и файлы вашей CRM-системы архивировались ежедневно",
	'LBL_RECOMMENDED_STEPS' => 'Рекомендованные шаги:',
	'LBL_RECOMMENDED_STEPS_TEXT' => 'Важно, чтобы вы выполнили следующие шаги',
	'LBL_DOCUMENTATION_TUTORIAL' => 'Документация и Инструкции',
	'LBL_WELCOME_FEEDBACK' => 'Мы ждем ваши отзывы',
	'LBL_TUTORIAL_TEXT' => 'Обзоры и учебники доступны на',
	'LBL_DROP_A_MAIL' => 'Пишите нам на',
	'LBL_LOGIN_PAGE' => 'Ваша страница входа: ',
	'Assets' => 'Активы',

);
?>
