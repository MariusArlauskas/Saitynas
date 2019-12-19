<template>
  <v-navigation-drawer permanent style="width: 100%; height: 97%">
    <v-content style="height: 100%; overflow-y: hidden">
      <v-list>
        <v-list-item v-if="isOpen" color="blue" @click.prevent="openNewMovieForm()">
          <v-list-item-content>New Movie</v-list-item-content>

          <v-list-item-action class="mt-0 mb-0">
            <v-list-item-icon>
              <v-icon>add</v-icon>
            </v-list-item-icon>
          </v-list-item-action>
        </v-list-item>

        <v-list-item v-if="openNewMovieFormValue">
          <NewMovie />
        </v-list-item>
      </v-list>
      <v-divider></v-divider>
      <v-list v-if="!openNewMovieFormValue" style="height: 87.3%; overflow-y: scroll">
        <v-list-item
          :to="{ name: 'movieGenres', params: { id: movie.id } }"
          v-for="(movie, key) in MOVIES"
          v-bind:key="key"
        >
          <v-list-item-content>
            <v-list-item-title>{{ movie.name }}</v-list-item-title>
          </v-list-item-content>
        </v-list-item>
      </v-list>

      <v-list v-if="openNewMovieFormValue" style="height: calc(46% - 128px); overflow-y: scroll">
        <v-list-item
          :to="{ name: 'movieGenres', params: { id: movie.id } }"
          v-for="(movie, key) in MOVIES"
          v-bind:key="key"
        >
          <v-list-item-content>
            <v-list-item-title>{{ movie.name }}</v-list-item-title>
          </v-list-item-content>
        </v-list-item>
      </v-list>
    </v-content>
  </v-navigation-drawer>
</template>

<script>
import NewMovie from "./NewMovie";
import { mapGetters } from "vuex";
export default {
  name: "movies",
  components: { NewMovie },
  data: () => ({}),
  computed: {
    ...mapGetters(["MOVIES"]),
    openNewMovieFormValue: {
      get() {
        return this.$store.getters.NEW_MOVIE_FORM;
      },
      set(value) {
        this.$store.commit("SET_NEW_MOVIE_FORM", value);
      }
    }
  },
  methods: {
    openNewMovieForm() {
      this.$store.commit("SET_NEW_MOVIE_FORM", true);
    },
    isOpen() {
      return this.$store.getters.NEW_MOVIE_FORM;
    }
  },
  mounted() {
    this.$store.dispatch("GET_MOVIES");
  }
};
</script>