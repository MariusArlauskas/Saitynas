<template>
  <v-navigation-drawer permanent style="width: 100%; height: 97%">
    <v-content style="height: 100%; overflow-y: hidden">
      <v-divider></v-divider>

      <v-list style="height: calc(46% - 128px); overflow-y: scroll">
        <v-list-item
          :to="{ name: 'myMovieGenres', params: { id: movie.movie_id, type: 'myMovies' } }"
          v-for="(movie, key) in MY_MOVIES"
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
import { mapGetters } from "vuex";
export default {
  name: "myMovies",
  components: { },
  data: () => ({}),
  computed: {
    ...mapGetters(["MY_MOVIES"])
  },
  methods: {},
  mounted() {
    this.$store.dispatch('GET_MY_MOVIES')
  }
};
</script>