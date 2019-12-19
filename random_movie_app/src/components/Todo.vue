<template>
  <div style="height: 96%">
    <v-container fluid fill-height pl-0 pr-0 pb-0 pt-0>
      <v-layout row align-space-between justify-space-between>
        <v-flex fill-height lg3 pr-2>
          <v-toolbar v-if="ROLE === 'ROLE_ADMIN'" dark color="blue">
            <v-select
              v-model="selectedType"
              :items="selectingAdmin"
              item-text="name"
              item-value="id"
              menu-props="auto"
              label="Select"
              hide-details
              prepend-icon="library_books"
              single-line
              @change="toggleTypeList()"
            ></v-select>
          </v-toolbar>

          <v-toolbar v-else dark color="blue">
            <v-select
              v-model="selectedType"
              :items="selecting"
              item-text="name"
              item-value="id"
              menu-props="auto"
              label="Select"
              hide-details
              prepend-icon="library_books"
              single-line
              @change="toggleTypeList()"
            ></v-select>
          </v-toolbar>
          <Genres v-if="SHOW_TYPE === 1" />
          <Movies v-if="SHOW_TYPE === 2" />
          <Users v-if="SHOW_TYPE === 3" />
          <!-- <MyMovies v-if="SHOW_TYPE === 4" /> -->
        </v-flex>
        <v-flex fill-height lg6 pr-2 pl-2>
            <router-view v-if="SHOW_TYPE === 1" name="genres" :key="$route.fullPath"></router-view>
            <router-view v-if="SHOW_TYPE === 2" name="movieGenres" :key="$route.fullPath"></router-view>
            <router-view v-if="SHOW_TYPE === 3" name="userMovies" :key="$route.fullPath"></router-view>
            <!-- <router-view v-if="SHOW_TYPE === 4" name="myMovieGenres" :key="$route.fullPath"></router-view> -->
        </v-flex>
        <v-flex fill-height lg3 pl-2>
          <OptionsBar />
        </v-flex>
      </v-layout>
    </v-container>
    <v-footer height="auto" color="indigo">
      <v-layout justify-center>
        <v-flex indigo text-xs-center white--text>
          &copy;2019
          <strong>M</strong>
        </v-flex>
      </v-layout>
    </v-footer>
    <Notification />
  </div>
</template>

<script>
import Genres from "./GenresFolder/Genres";
import Movies from "./MoviesFolder/Movies";
import Users from "./UsersFolder/Users";
// import MyMovies from "./MyMoviesFolder/MyMovies";
import Notification from "./Notification";
import OptionsBar from "./OptionsBarFolder/OptionsBar";
import { mapGetters } from "vuex";
export default {
  name: "todo",
  components: { Genres, Movies, Users, OptionsBar, Notification },
  data: () => ({
    selectedType: {
      id: 1
    },
    selectingAdmin: [
      { id: 1, name: "Genres" },
      { id: 2, name: "Movies" },
      { id: 3, name: "Users" }
      // { id: 4, name: "My Movies" }
    ],
    selecting: [
      { id: 1, name: "Genres" },
      { id: 2, name: "Movies" }
      // { id: 4, name: "My Movies" }
    ]
  }),
  computed: {
    ...mapGetters(["LISTS", "SHOW_TYPE", "ROLE"])
  },
  methods: {
    toggleTypeList() {
      this.$store.commit("SET_SHOW_TYPE", this.selectedType);
    }
  },
  mounted() {
    this.$store.dispatch("GET_LISTS");
  }
};
</script>
