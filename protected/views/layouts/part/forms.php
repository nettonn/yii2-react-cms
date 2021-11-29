<?php
/* @var $this app\components\View */
?>
<div style="display: none;">
    <form id="feedback-form" action="<?= yii\helpers\Url::to(['/ajax/feedback']) ?>"  method="post" class="forms ajax-form popup-form">
        <div class="title" data-default="Связаться с нами"></div>
        <div class="description" data-default="Напишите ваш вопрос или пожелание напрямую создателям сайта."></div>
        <div class="fields">
            <div class="field">
                <input type="text" name="name" placeholder="Ваше имя">
            </div>
            <div class="field">
                <input type="text" name="contact" placeholder="Способ связи с вами">
            </div>
            <div class="field">
                <textarea name="message" placeholder="Ваше сообщение" required></textarea>
            </div>

            <div class="submit">
                <button type="submit" class="btn">Отправить</button>
            </div>
        </div>
        <div class="message-success">
            <div class="text">Спасибо!<br> Ваше сообщение отправлено.</div>
        </div>
    </form>

    <form id="error-feedback-form" action="<?= yii\helpers\Url::to(['/ajax/error-feedback']) ?>"  method="post" class="forms ajax-form popup-form">
        <div class="title" data-default="Нашли ошибку на странице?"></div>
        <div class="description" data-default="Если вы обнаружили какие-то неточности или ошибки напишите нам. Можете оставить свой E-Mail, если вы хотите что то спросить."></div>
        <div class="fields">
            <div class="field">
                <textarea name="message" placeholder="Опишите ошибку или неточность" required></textarea>
            </div>
            <div class="submit">
                <button type="submit" class="btn">Отправить</button>
            </div>
        </div>
        <div class="message-success">
            <div class="text">Спасибо!<br> Ваше сообщение отправлено.</div>
        </div>
    </form>
</div>
