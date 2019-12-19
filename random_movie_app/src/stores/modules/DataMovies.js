import axios from "axios";

export default {
    state: {
        movies: []
    },
    getters: {
        MOVIES: state => {
            return state.movies;
        },
        MOVIE: state => index => {
            if (index) {
                return state.movies.find(movie => movie.id === index);
            }
        },
        GENRES: state => index => {
            if (index) {
                return state.movies.find(movie => movie.id === index).genres;
            }
        }
    },
    mutations: {
        SET_MOVIES: (state, payload) => {
            state.movies = payload;
        },
        ADD_MOVIE: (state, payload) => {
            state.movies.push(payload);
        },
        SET_GENRES: (state, { data, movieId }) => {
            state.movies.find(movies => movies.id === movieId).genres = data;
        },
        ADD_MOVIE_GENRE: (state, { data, movieId }) => {
            state.movies.find(movies => movies.id === movieId).genres.push(data)
        },
        REMOVE_MOVIE_BY_INDEX: (state, payload) => {
            var removeIndex = state.movies.map(function (item) { return item.id; })
                .indexOf(payload);
            state.movies.splice(removeIndex, 1);
        },
        REMOVE_MOVIE_GENRE_BY_INDEX: (state, { movieId, genreId }) => {
            var removeIndex = state.movies.find(movies => movies.id === movieId)
                .genres.map(function (item) { return item.genre_id; })
                .indexOf(genreId);
            state.movies.find(movies => movies.id === movieId)
                .genres.splice(removeIndex, 1);
        }
    },
    actions: {
        GET_MOVIES: async ({ commit }) => {
            let { data } = await axios.get(`movies`);
            commit("SET_MOVIES", data);
        },
        POST_MOVIE: ({ commit }, payload) => {
            return new Promise((resolve, reject) => {
                axios.post(`movies`, payload)
                    .then(({ data, status }) => {
                        commit("ADD_MOVIE", data)
                        if (status === 200) {
                            resolve({ data, status })
                        }
                    })
                    .catch(error => {
                        reject(error);
                    })
            })
        },
        DELETE_MOVIE: ({ commit }, payload) => {
            return new Promise((resolve, reject) => {
                axios.delete(`movies/${payload.movieId}`)
                    .then(({ data, status }) => {
                        commit("REMOVE_MOVIE_BY_INDEX", payload.movieId)
                        if (status === 200) {
                            resolve({ data, status })
                        }
                    })
                    .catch(error => {
                        reject(error);
                    })
            })
        },
        UPDATE_MOVIE: ({ commit }, payload) => {
            return new Promise((resolve, reject) => {
                axios.put(`movies/${payload.id}`, payload)
                    .then(({ data, status }) => {
                        if (commit && (true == false)) { return }
                        if (status === 200) {
                            resolve({ data, status })
                        }
                    })
                    .catch(error => {
                        reject(error);
                    })
            })
        },
        GET_MOVIE_GENRES: async ({ commit }, payload) => {
            try {
                let { data } = await axios.get(`movies/${payload}/genres`);
                commit("SET_GENRES", {
                    data,
                    movieId: payload
                });
            } catch (error) {
                console.log("No genres!")
            }
        },
        ADD_GENRE: ({ commit }, { movieId, genreToAdd }) => {
            return new Promise((resolve, reject) => {
                axios.post(`movies/${movieId}/genres`, {
                    genreId: genreToAdd
                })
                    .then(({ data, status }) => {
                        commit("ADD_MOVIE_GENRE", {
                            data,
                            movieId
                        })
                        if (status === 200) {
                            resolve({ data, status })
                        }
                    })
                    .catch(error => {
                        commit("SET_NOTIFICATION", {
                            display: true,
                            text: "Movie already has this genre!",
                            alertClass: "red"
                        })
                        reject(error);
                    })
            })
        },
        REMOVE_MOVIE_GENRE: async ({ commit }, { movieId, genreId }) => {
            return new Promise((resolve, reject) => {
                axios.delete(`movies/${movieId}/genres/${genreId}`)
                    .then(({ data, status }) => {
                        commit("REMOVE_MOVIE_GENRE_BY_INDEX", {
                            movieId,
                            genreId
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