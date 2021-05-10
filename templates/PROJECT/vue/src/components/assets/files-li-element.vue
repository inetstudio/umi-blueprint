<template>
    <li>
        <div class="img-wrap">
            <a :href="file.link" target="_blank">
                <img :src="file.image ? file.image.src : ''" alt="document">
            </a>
        </div>
        <div class="item-bottom">
            <p class="item-title">{{ file.file.name }}</p>

            <p class="item-date">{{ file.file.date }}</p>
            <p class="item-size">{{ file.file.size }}</p>
        </div>

        <p class="item-menu-btn" @click.stop="showWidget = !showWidget"></p>
        <transition name="slide-fade">
            <div v-if="showWidget" v-click-outside="hideWidget" class="item-menu" @click.stop>
                <a href="#" @click.prevent="">Отправить на почту</a>
                <a :href="file.link" @click.prevent="copyLink">Скопировать ссылку</a>
                <input class='hidden-field' type="text" :value="(getHostName + file.link)">
            </div>
        </transition>
    </li>
</template>

<script>
export default {
    name: 'FilesLiElement',
    data() {
        return {
            showWidget: false
        }
    },
    props: {
        file: {
            type: Object,
            required: true
        }
    },
    computed: {
        getHostName: function(){
            return window.location.hostname;
        }
    },
    methods: {
        copyLink: function(event) {
            let input = event.target.nextElementSibling;
            input.focus();
            input.select();

            document.execCommand('copy');

            this.showWidget = false;
            this.$parent.$snotify.success('Ссылка скопирована в буфер обмена', {
              timeout: 2000,
              showProgressBar: false,
              closeOnClick: false,
              pauseOnHover: true
            });
        },
        hideWidget() {
            if (this.showWidget) this.showWidget = false;
        },
    },
}
</script>

<style scoped>
li .item-menu {
    display: block !important;
}
.slide-fade-enter-active {
    transition: all .3s ease;
}
.slide-fade-leave-active {
    transition: all .8s cubic-bezier(1.0, 0.5, 0.8, 1.0);
}
.slide-fade-enter, .slide-fade-leave-to
    /* .slide-fade-leave-active до версии 2.1.8 */ {
    transform: translateX(10px);
    opacity: 0;
}
</style>