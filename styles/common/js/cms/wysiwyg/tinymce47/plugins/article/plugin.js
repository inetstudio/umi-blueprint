/**
 * plugin.js
 *
 * Copyright 2021 Inet Production, www.inetstudio.ru
 * @author Max Rakhmankin, Max Sakhuta
 */

/*jshint unused:false */

/*global tinymce:true */

tinymce.PluginManager.add('article', function (editor) {
    // Основные блоки
    editor.addButton('articleblock', {
        title: 'Статьи',
        text: '',
        type: 'menubutton',
        icon: 'editimage',
        menu: [
            {
                text: 'Блок: Содержание',
                onclick: function () {
                    insertMainBlock(editor, articleMenuBlock)
                }
            },
            {
                text: 'Элемент: Содержание',
                onclick: function () {
                    insertToParent(editor, '.article-page__side-list', articleMenuElement)
                }
            },
            {
                text: '|'
            },
            {
                text: 'Блок: Текстовый блок',
                onclick: function () {
                    insertMainBlock(editor, articleTextBlock)
                }
            },
            {
                text: 'Блок: Рекламный баннер',
                onclick: function () {
                    insertAdvertBlock(editor)
                }
            },
            {
                text: '|'
            },
            {
                text: 'Блок: Вопрос-ответ',
                onclick: function () {
                    insertMainBlock(editor, faqBlock)
                },
            },
            {
                text: 'Элемент: Вопрос-ответ',
                onclick: function () {
                    insertToParent(editor, '.faq .faq__list', faqElement)
                },
            },
            {
                text: '|'
            },
            {
                text: 'Блок: Читайте также',
                onclick: function () {
                    insertMainBlock(editor, articleReadMore)
                }
            },
            {
                text: 'Элемент: Читайте также',
                onclick: function () {
                    insertReadMoreElement(editor)
                }
            },
        ],
    });

});

//*********************************
// Шаблоны блоков

const faqBlock =
    `<div class="faq">
        <div class="hs-wr">
            <div class="faq__title">
                <h2>Вопросы-ответы</h2>
            </div>
            <div class="faq__list">
                <div class="faq-item opener" v-opener>
                    <div class="faq-item__header opener__header" data-toggle>
                        <div class="faq-item__title">Вопрос</div>
                        <div class="faq-item__arrow"></div>
                    </div>

                    <div class="opener__content" data-content>
                        <div class="faq-item__body">
                            <p><strong>Ответ</strong></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>`

const faqElement =
    `<div class="faq-item opener" v-opener>
        <div class="faq-item__header opener__header" data-toggle>
            <div class="faq-item__title">Заголовок вопроса</div>
            <div class="faq-item__arrow"></div>
        </div>

        <div class="opener__content" data-content>
            <div class="faq-item__body">
                <p><strong>Выделенный текст</strong></p>
                <p>Просто текст</p>
            </div>
        </div>
    </div>`

//-----------------------------------------

const articleMenuBlock =
    `<div class="article-page__side" data-sticky-container>
        <div class="article-page__side-nav" data-margin-top="90" data-sticky-for="767">
            <div class="article-page__side-menu">
                <div class="article-page__side-buttons">
                    <a class="article-page__side-btn" href="#form-appointment">Записаться</a>
                    <button class="article-page__side-btn article-page__side-btn--rev"
                            v-on:click="$refs['form-back-call-popup'].openModal($event)"
                    >Обратный звонок
                    </button>
                </div>

                <div class="article-page__side-contents">
                    <div class="article-page__side-title">Содержание:</div>

                    <ol class="article-page__side-list">
                        <li><a href="#article-section-1">Заголовок 1</a></li>
                        <li><a href="#article-section-2">Заголовок 2</a></li>
                    </ol>
                </div>
            </div>
        </div>
    </div>`

const articleMenuElement =
    `<li><a href="#article-section-1">Новый заголовок (поменяйте якорь)</a></li>`

const articleTextBlock =
    `<div class="article-text-section"">
        <div class="hs-wr">
            <h2>Заголовок H2</h2>
            <p>Какой-то текст</p>
            <h3>Заголовок H3</h3>
        </div>
    </div>`

const articleReadMore =
    `<div class="more-articles">
        <div class="hs-wr">
            <div class="more-articles__title">
                <h2>Читайте также</h2>
            </div>

            <div id="more-articles-slider" class="more-articles__slider hs-carousel ">
                <div class="swiper-container">
                    <div class="more-articles__list swiper-wrapper">

                    </div>
                    <div class="swiper-pagination"></div>
                </div>

                <div class="more-articles__carousel-btn swiper__btns">
                    <div id="more-articles__carousel-btn-prev" class="swiper-btn el-prev swiper__btn-prev">
                        <svg width="30" height="63" viewBox="0 0 30 63" fill="none"
                             xmlns="http://www.w3.org/2000/svg">
                            <path d="M28 2L4 31.5L28 61" stroke="#203482" stroke-width="5"/>
                        </svg>
                    </div>
                    <div id="more-articles__carousel-btn-next" class="swiper-btn el-next swiper__btn-next">
                        <svg width="30" height="63" viewBox="0 0 30 63" fill="none"
                             xmlns="http://www.w3.org/2000/svg">
                            <path d="M2 2L26 31.5L2 61" stroke="#203482" stroke-width="5"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>`
//**************************************

// Функции добавления специальных блоков
// Функция добавления главных блоков
function insertMainBlock(editor, insertedTemplate) {
    const dom = editor.dom
    const domBody = dom.doc.body
    const selectedBlock = editor.selection.getSelectedBlocks()[0]

    const parentsBlock = domBody.childNodes

    let isInserted = false

    parentsBlock.forEach(parent => {
        if (isChild(selectedBlock, parent)) {
            parent.insertAdjacentHTML('afterEnd', insertedTemplate);
            isInserted = true
        }
    })

    if (!isInserted) {
        const lastChild = domBody.lastChild
        if (lastChild.innerHTML === '&nbsp;' || dom.isEmpty(lastChild)) lastChild.remove()
        domBody.insertAdjacentHTML('beforeEnd', insertedTemplate);
    }
    // editor.insertContent(insertedTemplate)
}

// Функция добавления элементов в конкретного родителя (модифицированная)
function insertToParent(editor, parentClass, template) {
    const dom = editor.dom.doc
    const selectedBlock = editor.selection.getSelectedBlocks()[0]

    if (!selectedBlock) {
        editor.windowManager.alert('Создайте соответствующий блок-родитель и разместите в нем курсор!');
        return
    }

    let parentEl = dom.querySelector(parentClass)

    if (parentEl) {
        parentEl.insertAdjacentHTML('beforeEnd', template);
    } else {
        editor.windowManager.alert('Создайте соответствующий блок-родитель и разместите в нем курсор!');
    }
}

// **************************************
// Функция добавления рекламных баннеров
function insertAdvertBlock(editor) {
    editor.windowManager.open({
        title: 'Добавить баннер',
        body: [
            {
                type: 'filepicker',
                name: 'bannerBgPicture',
                label: 'Фоновое изображение',
                filetype: 'image',
                value: '/files/*.jpg',
            },
            {
                type: 'textbox',
                name: 'bannerTitle',
                label: 'Заголовок',
                value: '...',
                multiline: false, // textarea
            },
            {
                type: 'textbox',
                name: 'bannerText',
                label: 'Текст предложения',
                value: '...',
                multiline: false, // textarea
            },
            {
                type: 'textbox',
                name: 'bannerNote',
                label: 'Примечание',
                value: '...',
                multiline: false, // textarea
            }
        ],
        onsubmit: function (e) {
            const template =
                `<div class="article-banner" style="background: url('${e.data.bannerBgPicture}')">
                    <div class="hs-wr">
                        <div class="article-banner__inner">
                            <div class="article-banner__title">${e.data.bannerTitle}</div>
                            <div class="article-banner__offer">
                                <div class="article-banner__offer-text">${e.data.bannerText}</div>
                                <div class="article-banner__offer-button">
                                    <a class="hs-btn" href="#!">Записаться</a>
                                </div>
                            </div>
                            <div class="article-banner__note">${e.data.bannerNote}</div>
                        </div>
                    </div>
                </div>`
            insertMainBlock(editor, template)
        }
    });
}

// Функция элемента "Читайте также"
function insertReadMoreElement(editor) {
    editor.windowManager.open({
        title: 'Добавить изображение',
        body: [
            {
                type: 'filepicker',
                name: 'previewCardImgSrc',
                label: 'Изображение',
                filetype: 'image',
                value: '/images/',
            },
            {
                type: 'textbox',
                name: 'previewCardText',
                label: 'Описание',
                value: '',
                multiline: false, // textarea
            },
            {
                type: 'textbox',
                name: 'previewCardLink',
                label: 'Ссылка',
                value: '',
                multiline: false, // textarea
            }
        ],
        onsubmit: function (e) {
            const template =
                `<a class="article-preview-card swiper-slide" href="${e.data.previewCardLink}">
                    <div class="article-preview-card__inner">
                        <div class="article-preview-card__image">
                            <img src="${e.data.previewCardImgSrc}" alt="image">
                        </div>

                        <div class="article-preview-card__title">${e.data.previewCardText}</div>
                        <div class="article-preview-card__btn hs-btn">Читать далее...</div>
                    </div>
                </a>`
            insertToParent(editor, '.more-articles .swiper-wrapper', template)
        }
    });
}

// **************************************
// Assets Function
function showParent(el, cssClass) {
    if (el.parentNode && el.parentNode.tagName !== 'BODY') {
        if (el.parentNode.classList.contains(cssClass)) {
            return el.parentNode;
        } else {
            return showParent(el.parentNode, cssClass);
        }
    } else {
        return null;
    }
}

function isChild(obj, parentObj) {
    while (obj !== undefined && obj !== null && obj.tagName.toUpperCase() !== 'BODY') {
        if (obj === parentObj) {
            return true;
        }
        obj = obj.parentNode;
    }
    return false;
}