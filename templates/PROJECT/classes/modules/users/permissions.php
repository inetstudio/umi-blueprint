<?php
	/**
	 * Группы прав на функционал модуля
	 */
	$permissions = [
		/** Права на авторизацию */
		'login' => [
			'login_do_json', 'checkRestore',
		],
		/**
		 * Права на редактирование настроек
		 */
		'settings' => [
			'groupsList',
		],
	];
