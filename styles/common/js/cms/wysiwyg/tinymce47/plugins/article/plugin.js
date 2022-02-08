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
    editor.addButton('serviceblock', {
        title: 'Услуги',
        text: '',
        type: 'menubutton',
        icon: 'editimage',
        menu: [
            {
                text: 'Блок (Услуги): Приглашаем Вас',
                onclick: function () {
                    insertMainBlock(editor, servicesWelcome)
                }
            },
            {
                text: 'Блок (Услуги): Об услуге',
                onclick: function () {
                    insertMainBlock(editor, servicesAbout)
                }
            },
            {
                text: 'Блок (Услуги): О дополнительных исследованиях',
                onclick: function () {
                    insertMainBlock(editor, servicesAdditional)
                }
            },
            {
                text: 'Блок (Услуги): Как проходят исследования',
                onclick: function () {
                    insertMainBlock(editor, servicesResearchSteps)
                }
            },
            {
                text: 'Блок (Услуги): Необходимые анализы',
                onclick: function () {
                    insertMainBlock(editor, servicesNecessaryTests)
                }
            },
            {
                text: 'Блок (Услуги): Анализы',
                onclick: function () {
                    insertMainBlock(editor, servicesAnalyzes)
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
        ],
    });

    // Основные блоки
    editor.addButton('articleblock', {
        title: 'Статьи',
        text: '',
        type: 'menubutton',
        icon: 'editimage',
        menu: [
            {
                text: 'Блок: Вводный блок',
                onclick: function () {
                    insertMainBlock(editor, articleIntro)
                }
            },
            {
                text: '|'
            },
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
                text: 'Блок: Блок с иконками',
                onclick: function () {
                    insertMainBlock(editor, articleIconsBlock)
                }
            },
            {
                text: 'Блок: Текстовый блок',
                onclick: function () {
                    insertMainBlock(editor, articleTextBlock)
                }
            },
            {
                text: 'Блок: Инфографика',
                onclick: function () {
                    insertMainBlock(editor, articleInfographic)
                }
            },
            {
                text: 'Блок: Подготовка (Шаги)',
                onclick: function () {
                    insertMainBlock(editor, articleSteps)
                }
            },
            {
                text: 'Блок: Рекламный баннер',
                onclick: function () {
                    insertAdvertBlock(editor)
                }
            },
            {
                text: 'Блок: Врачи',
                onclick: function () {
                    insertMainBlock(editor, articleDoctors)
                }
            },
            {
                text: 'Блок: Записаться на обследование',
                onclick: function () {
                    insertMainBlock(editor, formExamination)
                }
            },
            {
                text: '|'
            },
            {
                text: 'Блок: Фотогалерея отделения',
                onclick: function () {
                    insertMainBlock(editor, articlePhotogalleryBlock)
                }
            },
            {
                text: 'Элемент: Фотогалерея отделения',
                onclick: function () {
                    insertPhotoGalleryElement(editor)
                }
            },
            {
                text: '|'
            },
            {
                text: 'Блок: Список услуг',
                onclick: function () {
                    insertMainBlock(editor, articleServicesList)
                }
            },
            {
                text: 'Элемент: Список услуг',
                onclick: function () {
                    insertServiceElement(editor)
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
            {
                text: '|'
            },
            {
                text: 'Блок: Лицензии',
                onclick: function () {
                    insertMainBlock(editor, articleLicenseBlock)
                }
            },
            {
                text: 'Элемент: Лицензии',
                onclick: function () {
                    insertLicenseElement(editor)
                }
            },
        ],
    });

});

//*********************************
// Шаблоны блоков
const servicesWelcome =
    `<div class="services-welcome">
    <div class="hs-wr">
        <div class="services-welcome__header">
            <div class="services-welcome__side">
                <div class="services-welcome__title section-title-sm">Приглашаем Вас </div>
                <div class="services-welcome__desc">
                    <p>пройти гастроскопию в максимально комфортных условиях — под седацией (в медикаментозном сне).</p>
                </div>
            </div>
            
            <div class="services-welcome__side">
                <div class="services-welcome__text">
                    <p>Анестезиолог внутривенно введет специальный препарат для Вашего погружения в глубокий кратковременный (до 30 минут) сон. В состоянии медикаментозного сна Вы не будете испытывать неприятных ощущений и дискомфорта во время проведения процедуры, а после пробуждения у Вас не появятся побочные реакции, как от общего наркоза.</p>
                </div>
            </div>
        </div>

        <img src="/images/services-welcome.jpg" alt="image">
    </div>
</div>`

const servicesAbout =
    `<div class="services-about">
    <div class="hs-wr">
        <div class="services-about__title section-title-sm">
            Гастроскопия в Эндоскопическом центре Боткинской больницы – это:
        </div>

        <div class="services-about__list">
            <div class="services-about__item">
                <div class="services-about__item-icon">
                    <img src="/images/cms/data/pages/ico/service-ico-1.svg" alt="service">
                </div>
                <p class="services-about__item-text">Бесплатный приём врача-терапевта перед процедурой</p>
            </div>

            <div class="services-about__item">
                <div class="services-about__item-icon">
                    <img src="/images/cms/data/pages/ico/service-ico-2.svg" alt="service">
                </div>
                <p class="services-about__item-text">Возможность пройти исследования в день обращения</p>
            </div>

            <div class="services-about__item">
                <div class="services-about__item-icon">
                    <img src="/images/cms/data/pages/ico/service-ico-3.svg" alt="service">
                </div>
                <p class="services-about__item-text">Врачи-эндоскописты и врачи-анестезиологи Боткинской больницы
                    с опытом большим работы</p>
            </div>

            <div class="services-about__item">
                <div class="services-about__item-icon">
                    <img src="/images/cms/data/pages/ico/service-ico-4.svg" alt="service">
                </div>
                <p class="services-about__item-text">Исследования без боли и неприятных ощущений</p>
            </div>

            <div class="services-about__item">
                <div class="services-about__item-icon">
                    <img src="/images/cms/data/pages/ico/service-ico-6.svg" alt="service">
                </div>
                <p class="services-about__item-text">Максимальное пребывание пациента в центре - 1,5 часа</p>
            </div>

            <div class="services-about__item">
                <div class="services-about__item-icon">
                    <img src="/images/cms/data/pages/ico/service-ico-5.svg" alt="service">
                </div>
                <p class="services-about__item-text">Готовность результатов исследования по окончанию процедуры</p>
            </div>
        </div>
    </div>
</div>`

const servicesAdditional =
    `<div class="services-additional">
    <div class="hs-wr">
        <div class="services-additional__title">
            Если врач назначит Вам дополнительные исследования, мы проведём их в центре бесплатно
        </div>

        <div class="services-additional__list">
            <div class="services-additional__item">
                <div class="services-additional__item-icon">
                    <img src="/images/service-additional-ico-1.svg" alt="service">
                </div>
                <p class="services-additional__item-text">Оптическая биопсия тканей во время обследования</p>
            </div>

            <div class="services-additional__item">
                <div class="services-additional__item-icon">
                    <img src="/images/service-additional-ico-2.svg" alt="service">
                </div>
                <p class="services-additional__item-text">Диагностика и удаление небольших новообразований за одну процедуру</p>
            </div>

            <div class="services-additional__item">
                <div class="services-additional__item-icon">
                    <img src="/images/service-additional-ico-3.svg" alt="service">
                </div>
                <p class="services-additional__item-text">Взятие тканей на биопсию для уточнения природы новообразования</p>
            </div>

            <div class="services-additional__item">
                <div class="services-additional__item-icon">
                    <img src="/images/service-additional-ico-4.svg" alt="service">
                </div>
                <p class="services-additional__item-text">Тест на бактерию Хеликобактер пилори (Helicobacter pylori)</p>
            </div>
        </div>
    </div>
</div>`

const servicesResearchSteps =
    `<div class="research-steps">
    <div class="hs-wr">
        <div class="research-steps__title section-title-lg">
            Как проходит исследование
        </div>

        <ul class="research-steps__list">
            <li class="research-steps__item">
                <div class="research-steps__item-decore"></div>
                <div class="research-steps__item-text">
                    <p><strong>Вы записываетесь на прием</strong> по телефону +7 (499) 490-03-03 или <a href="#form-appointment">через форму записи на сайте</a></p>
                </div>
            </li>
            <li class="research-steps__item">
                <div class="research-steps__item-decore"></div>
                <div class="research-steps__item-text">
                    <p><strong>В день исследования</strong> Вам нужно прийти за 15 минут до назначенного времени, чтобы успеть оформить документы.</p>
                </div>
            </li>
            <li class="research-steps__item">
                <div class="research-steps__item-decore"></div>
                <div class="research-steps__item-text">
                    <p><strong>После оформления медицинской карты</strong> Вы получите индивидуальный браслет, одежду для переодевания, ключ от шкафчика.</p>
                </div>
            </li>
            <li class="research-steps__item">
                <div class="research-steps__item-decore"></div>
                <div class="research-steps__item-text">
                    <p><strong>Вас проводят до процедурной</strong>, а после гастроскопии отвезут в палату пробуждения (в случае проведения исследования с внутривенной седацией).</p>
                </div>
            </li>
        </ul>
    </div>
</div>`

const servicesNecessaryTests =
    `<div class="services-necessary-tests">
    <div class="hs-wr">
        <div class="services-necessary-tests__title section-title-sm">
            Необходимые анализы:
        </div>

        <div class="services-necessary-tests__text">

            <ul class="services-necessary-tests__list">
                <li>Результат анализа крови на инфекционную группу: ВИЧ, RW и гепатиты В, С. Актуальный (срок: не более 1 месяца).</li>
                <li>Результаты предыдущих исследований  (если таковые проводились).</li>
                <li>Электрокардиограмма (срок: не более 1 месяца) при проведении процедуры под внутривенной седацией.</li>
                <li>Результаты дополнительных (при необходимости) исследований, назначенных лечащим врачом:
                    <ul>
                        <li>общий анализ крови (срок: не более 1 месяца),</li>
                        <li>биохимический анализ крови (срок: не более 14 дней),</li>
                        <li>коагулограмма (срок: не более 14 дней).</li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</div>`

const servicesAnalyzes =
    `<div class="services-analyzes">
    <div class="hs-wr">
        <div class="services-analyzes__header">
            <div class="services-analyzes__desc">
                <div class="services-analyzes__title section-title-sm">Для Вашего удобства, Вы можете сдать анализы платно в нашем центре</div>
                <div class="services-analyzes__header-text">
                    <p>Запишитесь на комплекс анализов по специальной цене, либо пройдите анализы отдельно:</p>
                </div>
            </div>

            <div class="services-analyzes__info">
                <div class="services-analyzes__info-price">
                    5 000&nbsp;&#8381;
                </div>
                <div class="services-analyzes__info-text">
                    стоимость комплекса анализов
                </div>
            </div>
        </div>

        <div class="services-analyzes__list">
            <div class="services-analyzes__item">
                <div class="services-analyzes__item-title">Общий анализ крови</div>
                <div class="services-analyzes__item-price">300&nbsp;&#8381;</div>
            </div>

            <div class="services-analyzes__item">
                <div class="services-analyzes__item-title">Коагулограмма</div>
                <div class="services-analyzes__item-price">500&nbsp;&#8381;</div>
            </div>

            <div class="services-analyzes__item">
                <div class="services-analyzes__item-title">Биохимия (в случае, если есть почечная или печеночная недостаточность).</div>
                <div class="services-analyzes__item-price">2 100&nbsp;&#8381;</div>
            </div>

            <div class="services-analyzes__item">
                <div class="services-analyzes__item-title">ВИЧ, RW, гепатиты В и С</div>
                <div class="services-analyzes__item-price">1 100&nbsp;&#8381;</div>
            </div>

            <div class="services-analyzes__item">
                <div class="services-analyzes__item-title">Электрокардиограмма</div>
                <div class="services-analyzes__item-price">1 100&nbsp;&#8381;</div>
            </div>

        </div>

        <div class="services-analyzes__note">Забор анализов проводится в Боткинской больнице.</div>
    </div>
</div>`

const faqBlock =
    `<div class="faq">
        <div class="hs-wr">
            <div class="faq__title">
                <h2>Вопросы-ответы</h2>
            </div>
            <div class="faq__list">
                <div class="faq-item opener" v-opener>
                    <div class="faq-item__header opener__header" data-toggle>
                        <div class="faq-item__title">Как проводится гастроскопия желудка</div>
                        <div class="faq-item__arrow"></div>
                    </div>
    
                    <div class="opener__content" data-content>
                        <div class="faq-item__body">
                            <p><strong>Гастроскоп — это тонкий гибкий прибор со встроенной микровидеокамерой. Вводят его пациенту
                                через рот и постепенно опускают в желудок. Изображение с камеры передается на широкий экран.
                                Высокое разрешение видео помогает провести точную диагностику.</strong></p>
                            <p>Перед началом процедуры производят местную анестезию корня языка и задней стенки глотки спреем-анестетиком, что позволяет мышцам горла расслабиться и уменьшить рвотный рефлекс.</p>
                            <p>Пациент «глотает» эндоскоп. Прибор достаточно тонкий, поэтому не вызывает трудностей ни с глотанием, ни с дыханием.</p>
                            <p>Врач проводит осмотр слизистой органов верхних отделов пищеварительного тракта, после чего выдает заключение с рекомендациями либо направлением на консультацию к специалистам для дальнейшего лечения.</p>
                            <p>Во время гастроскопии с внутривенной седацией пациент находится в состоянии кратковременного медикаментозного сна и просыпается после окончания исследования. Внутривенная анестезия не дает побочных эффектов, вызываемых общим наркозом. Уже через 20 минут после диагностики пациент может покинуть клинику.</p>
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
                <p><strong>Выделленный текст</strong></p>
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

const articleIntro =
    `<div class="article-intro">
        <div class="hs-wr">
            <div class="article-intro__header">
                <div class="article-intro__desc">
                    <div class="article-intro__title">
                        <h1>Гастроскопия <br> с седацией (во&nbsp;сне)</h1>
                    </div>
                </div>

                <div class="article-intro__info">
                    <div class="article-intro__info-text">
                        стоимость от
                    </div>
                    <div class="article-intro__info-price">
                        12 400&nbsp;&#8381;
                    </div>
                </div>
            </div>

            <p>Позволяет визуально обследовать слизистую оболочку пищевода, желудка и двенадцатиперстной
                кишки.
                Обследование проводится с помощью эндоскопа, который представляет собой гибкий зонд с
                встроенной
                видеокамерой, что позволяет врачу получать изображение на экране.</p>
        </div>
    </div>
    <div id="article-page-menu" class="article-page__page-menu">
        <span class="tinymce-edit-info">Не редактируйте этот блок. В нем будет автоматически размещено содержание для мобильной версии</span>
    </div>`

const articleIconsBlock =
    `<div class="services-about">
        <div div class="hs-wr">
            <h2>Гастроскопия в Эндоскопическом центре Боткинской больницы – это:</h2>

            <div class="services-about__list">
                <div class="services-about__item">
                    <div class="services-about__item-icon">
                        <img src="/images/cms/data/pages/ico/service-ico-1.svg" alt="service">
                    </div>
                    <p class="services-about__item-text">Бесплатный приём врача-терапевта перед
                        процедурой</p>
                </div>

                <div class="services-about__item">
                    <div class="services-about__item-icon">
                        <img src="/images/cms/data/pages/ico/service-ico-2.svg" alt="service">
                    </div>
                    <p class="services-about__item-text">Возможность пройти исследования в день
                        обращения</p>
                </div>

                <div class="services-about__item">
                    <div class="services-about__item-icon">
                        <img src="/images/cms/data/pages/ico/service-ico-3.svg" alt="service">
                    </div>
                    <p class="services-about__item-text">Врачи-эндоскописты и врачи-анестезиологи Боткинской
                        больницы
                        с опытом большим работы</p>
                </div>

                <div class="services-about__item">
                    <div class="services-about__item-icon">
                        <img src="/images/cms/data/pages/ico/service-ico-4.svg" alt="service">
                    </div>
                    <p class="services-about__item-text">Исследования без боли и неприятных ощущений</p>
                </div>

                <div class="services-about__item">
                    <div class="services-about__item-icon">
                        <img src="/images/cms/data/pages/ico/service-ico-5.svg" alt="service">
                    </div>
                    <p class="services-about__item-text">Максимальное пребывание пациента в центре - 1,5
                        часа</p>
                </div>

                <div class="services-about__item">
                    <div class="services-about__item-icon">
                        <img src="/images/cms/data/pages/ico/service-ico-6.svg" alt="service">
                    </div>
                    <p class="services-about__item-text">Готовность результатов исследования по окончанию
                        процедуры</p>
                </div>
            </div>
        </div>
    </div>`

const articleTextBlock =
    `<div class="article-text-section"">
        <div class="hs-wr">
            <h2>Заголовок H2</h2>
            <p>Какойто текст</p>
            <h3>Заголовок H3</h3>
        </div>
    </div>`

const articleInfographic =
    `<div class="article-numbers">
        <div class="hs-wr">
            <div class="article-numbers__list">
                <div class="article-numbers__item">
                    <div class="article-numbers__item-header">
                        <div class="article-numbers__item-number">10</div>
                        <div class="article-numbers__item-number-desc">мин.</div>
                    </div>
                    <p>Продолжительность обследования</p>
                </div>

                <div class="article-numbers__item">
                    <div class="article-numbers__item-header">
                        <div class="article-numbers__item-number">20</div>
                        <div class="article-numbers__item-number-desc">мин.</div>
                    </div>
                    <p>Время подготовки заключения</p>
                </div>

                <div class="article-numbers__item">
                    <div class="article-numbers__item-header">
                        <div class="article-numbers__item-number">1</div>
                        <div class="article-numbers__item-number-desc">день</div>
                    </div>
                    <p>Результаты проведения процедуры</p>
                </div>
            </div>
        </div>
    </div>`

const articleSteps =
    `<div class="research-steps" id="article-section-6">
        <div class="hs-wr">
            <h2>Подготовка к гастроскопии во сне</h2>

            <ul class="research-steps__list">
                <li class="research-steps__item">
                    <div class="research-steps__item-decore"></div>
                    <div class="research-steps__item-text">
                        <p>Текст внутри блока</p>
                    </div>
                </li>

                <li class="research-steps__item">
                    <div class="research-steps__item-decore"></div>
                    <div class="research-steps__item-text">
                        <p>Текст внутри блока</p>
                    </div>
                </li>

                <li class="research-steps__item">
                    <div class="research-steps__item-decore"></div>
                    <div class="research-steps__item-text">
                        <p>Текст внутри блока</p>
                    </div>
                </li>

                <li class="research-steps__item">
                    <div class="research-steps__item-decore"></div>
                    <div class="research-steps__item-text">
                        <p>Текст внутри блока</p>
                    </div>
                </li>
            </ul>
        </div>
    </div>`

const articlePhotogalleryBlock =
    `<div class="hs-carusel-img">
        <div class="hs-wr">
            <h2 class="hs-carusel-img__header">Фотогалерея отделения</h2>

            <div class="hs-carusel-img__swiper">
                <div id="carusel-img--in-article" class="hs-carusel-img__carusel swiper-container">
                    <div class="swiper-wrapper">
                        
                    </div>
                </div>
                
                <div class="hs-carusel-img__carousel-btn swiper__btns">
                    <div id="carusel-img-prev--in-article" class="swiper-btn el-prev swiper__btn-prev">
                        <svg width="30" height="63" viewBox="0 0 30 63" fill="none"
                             xmlns="http://www.w3.org/2000/svg">
                            <path d="M28 2L4 31.5L28 61" stroke="#203482" stroke-width="5"/>
                        </svg>
                    </div>
                    <div id="carusel-img-next--in-article" class="swiper-btn el-next swiper__btn-next">
                        <svg width="30" height="63" viewBox="0 0 30 63" fill="none"
                             xmlns="http://www.w3.org/2000/svg">
                            <path d="M2 2L26 31.5L2 61" stroke="#203482" stroke-width="5"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>`

const articleServicesList =
    `<div class="article-services">
        <div class="hs-wr">
            <h2 class="article-services__title">Для Вашего удобства, Вы можете пройти обследования в нашем
                центре</h2>

            <div class="article-services__list">
                <div class="article-services__list-head">
                    <div class="article-services__list-row">
                        <div class="article-services__list-cell">
                            <p>Услуга</p>
                        </div>
                        <div class="article-services__list-cell">
                            <p>Стоимость (руб)</p>
                        </div>
                    </div>
                </div>

                <div class="article-services__list-body">
                    <a class="article-services__list-item article-services__list-row" href="#!">
                        <div class="article-services__list-cell">
                            <p>Гастроскопия лечебно-диагностическая (в т.ч биопсией) в условиях дневного стационара эндоскопического центра</p>
                        </div>
                        <div class="article-services__list-cell">
                            <p><strong>4 800</strong></p>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>`

const articleDoctors =
    `<div class="hs-doctor-img">
        <div class="hs-wr">
            <h2 class="hs-doctor-img__header">Наша команда специалистов</h2>

            <doctor-img-list
                    url="/udata/content/getDoctorsPages/.json"
                    page="article"
            ><span class="tinymce-edit-info">Блок - Врачи. Не редактируйте этот блок, данные загрузятся автоматически</span></doctor-img-list>
        </div>
    </div>`

const formExamination =
    `<div id="form-appointment" class="form-examination-section">
        <div class="hs-wr">
            <form-examination><span class="tinymce-edit-info">Форма - Записаться на обследование. Не редактируйте этот блок, данные загрузятся автоматически</span></form-examination>
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

const articleLicenseBlock =
    `<div id="license" class="hs-carusel-license">
        <div class="hs-wr">
            <h2 class="hs-carusel-license__header">
                Лицензии
            </h2>
            <div class="hs-carusel-license__swiper">
                <div id="carusel-license" class="hs-carusel-license__carusel swiper-container">
                    <div class="swiper-wrapper">
                       
                    </div>
                </div>
                <div class="hs-carusel-license__carousel-btn swiper__btns">
                    <div id="carusel-license-prev" class="swiper-btn el-prev swiper__btn-prev">
                        <svg width="30" height="63" viewBox="0 0 30 63" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M28 2L4 31.5L28 61" stroke="#203482" stroke-width="5"/>
                        </svg>
                    </div>
                    <div id="carusel-license-next" class="swiper-btn el-next swiper__btn-next">
                        <svg width="30" height="63" viewBox="0 0 30 63" fill="none" xmlns="http://www.w3.org/2000/svg">
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

//**************************************
//Фннкция элемента "Фотогалерея отделения"
function insertPhotoGalleryElement(editor) {
    editor.windowManager.open({
        title: 'Добавить изображение',
        body: [
            {
                type: 'filepicker',
                name: 'caruselImgSrc',
                label: 'Изображение',
                filetype: 'image',
                value: '/images/',
            }
        ],
        onsubmit: function (e) {
            const template =
                `<div class="hs-carusel-img__item swiper-slide">
                    <img src="${e.data.caruselImgSrc}" alt="image">
                </div>`
            insertToParent(editor, '.hs-carusel-img .swiper-wrapper', template)
        }
    });
}

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
                value: '/files/advert-bg-1.jpg',
            },
            {
                type: 'textbox',
                name: 'bannerTitle',
                label: 'Заголовок',
                value: 'Гастроскопия и колоноскопия «во сне»',
                multiline: false, // textarea
            },
            {
                type: 'textbox',
                name: 'bannerText',
                label: 'Текст предложения',
                value: 'СКИДКА 20% С&nbsp;13:00&nbsp;ДО&nbsp;21:00',
                multiline: false, // textarea
            },
            {
                type: 'textbox',
                name: 'bannerNote',
                label: 'Примечание',
                value: 'Предложение действительно с 1 по 30 сентября 2021 г.',
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

//Фннкция элемента "Список услуг"
function insertServiceElement(editor) {
    editor.windowManager.open({
        title: 'Добавить услугу',
        body: [
            {
                type: 'textbox',
                name: 'serviceTitle',
                label: 'Название услуги',
                value: '',
                multiline: true, // textarea
            },
            {
                type: 'textbox',
                name: 'servicePrice',
                label: 'Цена услуги',
                value: '',
                multiline: false, // textarea
            },
            {
                type: 'textbox',
                name: 'serviceLink',
                label: 'Ссылка на услугу',
                value: '',
                multiline: false, // textarea
            },
        ],
        onsubmit: function (e) {
            const template =
                `<a class="article-services__list-item article-services__list-row" href="${e.data.serviceLink}">
                    <div class="article-services__list-cell">
                        <p>${e.data.serviceTitle}</p>
                    </div>
                    <div class="article-services__list-cell">
                        <p><strong>${e.data.servicePrice}</strong></p>
                    </div>
                </a>`
            insertToParent(editor, '.article-services .article-services__list-body', template)
        }
    });
}

//Фннкция элемента "Читайте также"
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

//Фннкция элемента "Фотогалерея отделения"
function insertLicenseElement(editor) {
    editor.windowManager.open({
        title: 'Добавить изображение',
        body: [
            {
                type: 'filepicker',
                name: 'caruselImgSrc',
                label: 'Изображение',
                filetype: 'image',
                value: '/images/',
            }
        ],
        onsubmit: function (e) {
            const template =
                `<div class="hs-carusel-license__item swiper-slide">
                    <img src="${e.data.caruselImgSrc}" alt="image">
                </div>`
            insertToParent(editor, '.hs-carusel-license .swiper-wrapper', template)
        }
    });
}

//**************************************
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