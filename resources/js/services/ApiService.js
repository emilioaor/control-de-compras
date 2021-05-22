const ApiService = {
    get(url) {
        return new Promise((resolve, reject) => {
            axios.get(url).then(res => {
                resolve(res);
            }).catch(err => {
                console.log(err);
                if (err.response.status === 401 || err.response.status === 403) {
                    location.href = '/';
                }

                reject(err);
            })
        })
    },

    post(url, payload) {
        return new Promise((resolve, reject) => {
            axios.post(url, payload).then(res => {
                resolve(res);
            }).catch(err => {
                console.log(err);
                if (err.response.status === 401 || err.response.status === 403) {
                    location.href = '/';
                }

                reject(err);
            })
        })
    },

    put(url, payload) {
        return new Promise((resolve, reject) => {
            axios.put(url, payload).then(res => {
                resolve(res);
            }).catch(err => {
                console.log(err);
                if (err.response.status === 401 || err.response.status === 403) {
                    location.href = '/';
                }

                reject(err);
            })
        })
    },

    delete(url) {
        return new Promise((resolve, reject) => {
            axios.delete(url).then(res => {
                resolve(res);
            }).catch(err => {
                console.log(err);
                if (err.response.status === 401 || err.response.status === 403) {
                    location.href = '/';
                }

                reject(err);
            })
        })
    }
};

export default ApiService;