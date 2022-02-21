<template>
<div>
    <form @submit.prevent="getShortUrl">
        <div class="mb-3">
            <label for="exampleInputEmail1" class="form-label">URL</label>
            <input v-model="longUrl" type="text" class="form-control">
            <div class="form-text">Enter your long URL Here.</div>

            <button type="submit" class="btn btn-primary">Submit</button>
        </div>

        <p>{{responseMessage}}</p>
    </form>
</div>
</template>

<script>
export default {
    data(){
        return {
            longUrl: null,
            responseMessage: null
        }
    },
    methods: {
        getShortUrl(){
        this.errors = {};
        axios.post('/api/generate-short-url', {
                long_url: this.longUrl
            })
            .then(response => {
                if(response.data.has_error){
                    this.responseMessage = response.data.message;
                }else{
                    this.responseMessage = response.data.data.short_url
                }
                console.log(response);
            })
            .catch((error) => {
                this.errors = error.response.data.errors
            })
        },
    }
}
</script>

