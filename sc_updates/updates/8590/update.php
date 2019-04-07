<?php

UPDATE_LanguageService::getInstance()->deleteLangKey('admin', 'input_settings_allsc_photo_upload_label');
UPDATE_ConfigService::getInstance()->deleteConfig('base', 'tf_allsc_pic_upload');