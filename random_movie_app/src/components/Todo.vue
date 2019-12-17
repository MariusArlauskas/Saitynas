<template>
  <div style="max-height: 96.1vh; height: 100%">
    <v-container fluid fill-height pl-0 pr-0 pb-0 pt-0>
      <v-layout row align-space-between justify-space-between>
        <v-flex fill-height lg3 pr-2>
          <v-toolbar dark color="blue">
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
            

            <!-- <v-toolbar-title v-if="!DISPLAY_SEARCH_LIST">Genres</v-toolbar-title> -->
            <SearchBar v-if="DISPLAY_SEARCH_LIST" />
            <v-spacer></v-spacer>
            <v-btn icon @click.prevent="toggleSearchList()">
              <v-icon>search</v-icon>
            </v-btn>
          </v-toolbar>
          <Genres v-if="SHOW_TYPE === 1"/>
          <Movies v-if="SHOW_TYPE === 2"/>
          <Users v-if="SHOW_TYPE === 3"/>
        </v-flex>
        <v-flex fill-height lg6 pr-2 pl-2>
          <router-view v-if="SHOW_TYPE === 1" name="tasks" :key="$route.fullPath"></router-view>
          <router-view v-if="SHOW_TYPE === 2" name="movieGenres" :key="$route.fullPath"></router-view>
          <router-view v-if="SHOW_TYPE === 3" name="userMovies" :key="$route.fullPath"></router-view>
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
import SearchBar from "./SearchBar";
import Genres from "./GenresFolder/Genres";
import Movies from "./MoviesFolder/Movies";
import Users from "./UsersFolder/Users";
import Notification from "./Notification";
import OptionsBar from "./OptionsBar";
import { mapGetters } from "vuex";
export default {
  name: "todo",
  components: { Genres, Movies, Users, OptionsBar, Notification, SearchBar },
  data: () => ({
    selectedType: {
      id: 1
    },
    selecting: [
      { id: 1, name: "Genres"}, 
      { id: 2, name: "Movies"},
      { id: 3, name: "Users"},
    ]
  }),
  computed: {
    ...mapGetters(["DISPLAY_SEARCH_LIST", "LISTS", "SHOW_TYPE"])
  },
  methods: {
    toggleSearchList() {
      this.$store.commit("SET_DISPLAY_SEARCH_LIST", !this.DISPLAY_SEARCH_LIST);
    },
    toggleTypeList() {
      this.$store.commit("SET_SHOW_TYPE", this.selectedType);
    }
  },
    mounted() {
    this.$store.dispatch("GET_LISTS");
  }
};
</script>