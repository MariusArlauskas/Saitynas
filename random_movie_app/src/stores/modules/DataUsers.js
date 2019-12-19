import axios from "axios";

export default {
    state: {
        users: []
    },
    getters: {
        USER: state => index => {
            if (index) {
                return state.users.find(user => user.id === index);
            }
        },
        USERS: state => {
            return state.users;
        },
        USER_TITLE: state => index => {
            if (index) {
                return state.users.find(user => user.id === index)["username"];
            }
        },
        USER_MOVIES: state => index => {
            if (index) {
                return state.users.find(user => user.id === index).movies;
            }
        }
    },
    mutations: {
        SET_USERS: (state, payload) => {
            state.users = payload;
        },
        ADD_USER: (state, payload) => {
            state.users.push(payload);
        },
        SET_USER_MOVIES: (state, { data, userId }) => {
            state.users.find(users => users.id === userId).movies = data;
        },
        REMOVE_USER_MOVIE_BY_INDEX: (state, { userId, movieId }) => {
            state.users.find(user => user.id === userId).favorites_count = state.users.find(user => user.id === userId).favorites_count - 1
            var removeIndex = state.users.find(user => user.id === userId)
                .movies.map(function (item) { return item.genre_id; })
                .indexOf(movieId);
            state.users.find(user => user.id === userId)
                .movies.splice(removeIndex, 1);
        }
    },
    actions: {
        GET_USERS: async ({ commit }) => {
            let { data } = await axios.get(`users`);
            commit("SET_USERS", data);
        },
        POST_USER: ({ commit }, payload) => {
            return new Promise((resolve, reject) => {
                axios.post(`users`, payload)
                    .then(({ data, status }) => {
                        let object = {
                            name: payload["name"],
                            description: "500",
                            id: data.split(" ")[data.split(" ").length - 1],
                            users_count: "0"
                        }
                        commit("ADD_USER", object)
                        if (status === 201) {
                            resolve({ object, status })
                        }
                    })
                    .catch(error => {
                        reject(error);
                    })
            })
        },
        GET_USER_MOVIES: async ({ commit }, payload) => {
            try {
                let { data } = await axios.get(`users/${payload}/movies`);
                commit("SET_USER_MOVIES", {
                    data,
                    userId: payload
                });
            } catch (error) {
                console.log("No movies!")
            }
        },
        REMOVE_USER_MOVIE: async ({ commit }, { userId, movieId }) => {
            return new Promise((resolve, reject) => {
                axios.delete(`users/${userId}/movies/${movieId}`)
                    .then(({ data, status }) => {
                        commit("REMOVE_USER_MOVIE_BY_INDEX", {
                            userId,
                            movieId
                        })
                        if (status === 200) {
                            resolve({ data, status })
                        }
                    })
                    .catch(error => {
                        reject(error);
                    })
            })
        }
    }
};