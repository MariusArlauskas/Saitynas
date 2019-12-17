import Vue from 'vue';
import vuetify from './plugins/vuetify';
import VueRouter from 'vue-router';
import axios from 'axios'
import store from './stores/store'
import App from './App.vue';

import Login from './components/Auth/Login'
import Signup from './components/Auth/Signup'
import ToDo from './components/Todo'
import Tasks from './components/GenresFolder/Tasks'
import NotesModal from './components/NotesModal'
import MovieGenres from './components/MoviesFolder/MovieGenres'
import MoviesModal from './components/MoviesFolder/MoviesModal'
import MovieGenreInfoModal from './components/MoviesFolder/MovieGenreInfoModal'
import UserMovies from './components/UsersFolder/UserMovies'

Vue.config.productionTip = false;

Vue.use(VueRouter);

axios.defaults.baseURL = 'http://127.0.0.1:8000/api/';
axios.defaults.withCredentials = true;

const routes = [
  {
    path: '/',
    component: ToDo,
    name: "todo",
    children: [
      {
        path: 'genre/:id',
        components: { tasks: Tasks },
        name: "tasks",
        children: [
          {
            path: 'movie/:taskId',
            components: { notes: NotesModal },
            name: "notes"
          }
        ]
      },
      {
        path: 'movie/:id',
        components: {  movieGenres : MovieGenres },
        name: "movieGenres",
        children: [
          {
            path: '',
            components: { moviesEdit: MoviesModal },
            name: "moviesEdit"
          },
          {
            path: 'genre/:genreId',
            components: { movieGenreInfoModal: MovieGenreInfoModal },
            name: "movieGenreInfoModal"
          }
        ]
      },
      {
        path: 'user/:id',
        components: {  userMovies : UserMovies },
        name: "userMovies",
        children: [
          {
            path: 'movie/:movieId',
            components: { notes: NotesModal },
            name: "UserAndMovies"
          }
        ]
      }
    ]
  },
  {
    path: '/login',
    component: Login,
    name: "login"
  },
  {
    path: '/signup',
    component: Signup,
    name: "signup"
  }
];

const router = new VueRouter({
  mode: 'history',
  routes,
  base: '/'
})

new Vue({
  router,
  store,
  vuetify,
  render: h => h(App)
}).$mount('#app')