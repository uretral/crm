<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header" @click="getUsers()">Example Component</div>

                    <div class="card-body" >
                        I'm an example component.
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: 'ExampleComponent',
        methods: {
            getUsers () {
                alert('fdsfdf');


            }
        },
        mounted() {
            console.log('Component mounted.')
        }
    }
</script>


