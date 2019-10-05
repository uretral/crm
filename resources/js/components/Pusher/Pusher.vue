<template>
    <div>

    </div>
</template>

<script>
    import { Notification } from 'uiv'
    export default {
        props: [],
        data: function () {
            return {}
        },
        components:{
            Notification
        },
        methods: {
            playSound(){
                let audio = new Audio(); // Создаём новый элемент Audio
                audio.src = '/sounds/piano.mp3'; // Указываем путь к звуку "клика"
                audio.autoplay = true; // Автоматически запускаем
            },
            init:function () {
                let delay = 10000;

                let timerId = setTimeout(function request() {
                    let audio = new Audio(); // Создаём новый элемент Audio
                    audio.src = '/sounds/piano.mp3'; // Указываем путь к звуку "клика"
                    audio.autoplay = true; // Автоматически запускаем


                     Notification.notify({
                        title: 'Title',
                        content: 'This notification will not dismiss automatically.',
                        duration: 0,
                        placement: 'bottom-left',
                         offsetX: 100
                    }).then(() => {
                        // resolve after dismissed
                        // console.log('dismissed')
                    });


                    // if (ошибка запроса из-за перегрузки сервера) {
                    //     // увеличить интервал для следующего запроса
                    //     delay *= 2;
                    // }

                    timerId = setTimeout(request, delay);

                }, delay);
            }
        },
        mounted() {
            // this.init();
        }
    };
</script>
