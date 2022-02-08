/**
 * Глобальные кастомные настройки tinyMCE 4.7.
 * @link https://www.tinymce.com/docs/configure/
 *
 * Эти настройки имеют больший приоритет, чем настройки по умолчанию
 * (@see WYSIWYG.prototype.tinymce47.settings), и будут применяться
 * при каждой инициализации визуального редактора (@see WYSIWYG.prototype.tinymce47.init).
 *
 * Чтобы кастомизировать каждую инициализацию отдельно, нужно использовать
 * локальные кастомные настройки. Для этого нужно кастомизировать шаблон,
 * в котором происходит инициализация визуального редактора.
 *
 * Алгоритм применения настроек при каждой инициализации:
 *   * Сначала применяются настройки по умолчанию
 *   * Потом применяются глобальные кастомные настройки
 *   * Потом применяются локальные кастомные настройки
 *
 * При совпадении ключей настроек, приоритет имеет более позднее значение.
 * Сравнение значений не глубокое, т.е. при совпадении ключей
 * будет перезаписано все значение, а не его часть.
 *
 * Пример локальных кастомных настроек из файла /styles/skins/_eip/js/popup.js :
 *
 * // Кастомизирует ширину и высоту html-редактора
 * // Остальные настройки такие же, как дефолтные,
 * // @see WYSIWYG.prototype.tinymce47.settings
 * uAdmin('settings', {
 * 	codemirror: {
 * 		indentOnInit: true,
 * 		path: 'codemirror',
 * 		width: 700,
 * 		height: 400,
 * 		config: {
 * 			lineNumbers: true,
 * 			lineWrapping: true,
 * 			autofocus: true,
 * 		}
 * 	}
 * },'wysiwyg');
 *
 * uAdmin('type', 'tinymce47', 'wysiwyg');
 *
 * @type {Object}
 */

window.mceCustomSettings = {

	// Файл с кастомным CSS
	// @link https://www.tinymce.com/docs/configure/content-appearance/#content_css
	content_css : [
		'/styles/common/js/cms/wysiwyg/tinymce47/tinymce_custom.css',
		'/templates/endomos/public/dist/css/styles.css',
	],
	external_plugins: {
		'codemirror': '/styles/common/js/cms/wysiwyg/tinymce47/plugins/codemirror/plugin.min.js',
		'article': '/styles/common/js/cms/wysiwyg/tinymce47/plugins/article/plugin.js',
	},
	plugins: [
		"anchor",
		"advlist",
		"charmap",
		// "code",
		"codemirror",
		"contextmenu",
		// "directionality",
		// "emoticons",
		"fullscreen",
		// "hr",
		"image",
		// "insertdatetime",
		"link",
		"lists",
		"media",
		// "nonbreaking",
		// "noneditable",
		// "pagebreak",
		"paste",
		// "print",
		// "save",
		"searchreplace",
		// "spellchecker",
		"table",
		// "template",
		"textcolor",
		"visualblocks",
		"preview",
		"article",
		// "autoresize"
	],
	// Тулбар
	toolbar: 'paste pastetext undo redo removeformat link unlink anchor image media table code blockquote ' +
		'formatselect fontselect fontsizeselect bold italic strikethrough underline alignleft aligncenter alignright ' +
		'alignjustify bullist numlist outdent indent forecolor backcolor | visualblocks preview serviceblock articleblock',

	menubar: false,

	visualblocks_default_state: true,

	// Расширение разрешенных html-элементов
	extended_valid_elements : "a[*],button[*|v-on],template[*],span[*],div[*|v-opener|v-opener.mobile],svg[*],path[*],form-back-call[*],section-yandex-map[*],form-request[*],doctor-img-list[*],form-examination[*],line[*],defs[*],radialGradient[*],radial-gradient[*],circle[*],stop[*]",

	custom_elements : 'section-yandex-map,form-back-call,form-request,doctor-img-list,form-examination',

	forced_root_block : "div",

	init_instance_callback: function (editor) {
		const customElements = ['section-yandex-map', 'form-back-call', 'form-request', 'doctor-img-list', 'form-examination']

		editor.on('KeyUp', function (e) {
			let editor = tinyMCE.activeEditor
			const dom = editor.dom

			if(e.keyCode === 13) {
				const parentBlock = editor.selection.getSelectedBlocks()[0]

				if (customElements.includes(parentBlock.nodeName.toLowerCase())) {
					let newBlock = dom.create('div')
					newBlock.innerHTML = '<br data-mce-bogus="1">';
					dom.insertAfter(newBlock, parentBlock)
					parentBlock.remove()

					let rng = dom.createRng();
					newBlock.normalize();
					rng.setStart(newBlock, 0);
					rng.setEnd(newBlock, 0);
					editor.selection.setRng(rng);
				}
			}
		})
		editor.on('KeyDown', function (e) {
			if(e.keyCode === 27) {
				let editor = tinyMCE.activeEditor
				const dom = editor.dom
				const parentBlock = editor.selection.getSelectedBlocks()[0]
				const containerBlock = parentBlock.parentNode.nodeName === 'BODY' ? dom.getParent(parentBlock, dom.isBlock) : dom.getParent(parentBlock.parentNode, dom.isBlock)
				let newBlock = dom.create('div')
				newBlock.innerHTML = '<br data-mce-bogus="1">';
				dom.insertAfter(newBlock, containerBlock)

				let rng = dom.createRng();
				newBlock.normalize();
				rng.setStart(newBlock, 0);
				rng.setEnd(newBlock, 0);
				editor.selection.setRng(rng);
			}
		});
	},
};

$('head').append('<link type="text/css" rel="stylesheet" href="/styles/common/js/cms/wysiwyg/tinymce47/plugins/article/css/article.css">');