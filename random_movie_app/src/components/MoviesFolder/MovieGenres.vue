<template>
  <div style="height: 100%">
    <v-card style="height: 100%; overflow: hidden">
      <v-toolbar color="blue" dark>
        <v-toolbar-title>{{ movieTitle }}</v-toolbar-title>
      </v-toolbar>

      <v-container style="height: 40%" class="ml-5 mr-5">
        <v-row>
          <v-col cols="9">
            <v-row>
              <v-content class="font-weight-black">{{ currentMovie["name"] }}</v-content>
            </v-row>
            <v-row
              class="subtitle-2"
            >{{ currentMovie["author"] }} || {{ currentMovie["release_date"] }}</v-row>
          </v-col>
          <v-col cols="1">
            <v-btn @click.prevent="openEditModal" color="primary" icon>
              <v-icon>edit</v-icon>
            </v-btn>
          </v-col>
          <v-col>
            <v-btn @click.prevent="deleteMovie" cols="1" icon color="pink">
              <v-icon>delete</v-icon>
            </v-btn>
          </v-col>
        </v-row>
        <v-row
          style="width: 98%; max-height: 77%; height: 77%; overflow-y: scroll"
        >{{ currentMovie["description"] }}</v-row>
      </v-container>
      <v-divider></v-divider>
      <v-list two-line style="height: calc(59% - 128px); overflow-y: scroll">
        <template v-for="(movieGenre, key) in GENRES">
          <MovieGenre v-bind:key="key" :movieGenre="movieGenre" :index="key" />
        </template>
      </v-list>
      <v-divider></v-divider>
      <v-card-actions>
        <v-layout>
          <v-flex>
            <NewMovieGenre />
          </v-flex>
        </v-layout>
      </v-card-actions>
    </v-card>
    <router-view name="moviesEdit"></router-view>
    <router-view name="movieGenreInfoModal"></router-view>
  </div>
</template>

<script>
import MovieGenre from "./MovieGenre";
import NewMovieGenre from "./NewMovieGenre";
export default {
  name: "movieGenres",
  components: { MovieGenre, NewMovieGenre },
  data: () => ({}),
  methods: {
    openEditModal() {
      this.$router.push({
        name: "moviesEdit"
      });
    },
    async deleteMovie() {
      await this.$store
        .dispatch("DELETE_MOVIE", {
          movieId: this.$route.params.id
        })
        .then(() => {
          this.$store.commit("SET_NOTIFICATION", {
            display: true,
            text: "Movie has been removed!",
            alertClass: "warning"
          })
          this.$store.dispatch("GET_MOVIES");
          this.$router.push({
            path: "../"
          });
        })
    }
  },
  computed: {
    movieTitle() {
      return this.$store.getters.MOVIE_TITLE(this.$route.params.id);
    },
    currentMovie() {
      return this.$store.getters.MOVIE(this.$route.params.id);
    },
    GENRES() {
      return this.$store.getters.GENRES(this.$route.params.id);
    }
  },
  async mounted() {
    await this.$store.dispatch("GET_GENRES", this.$route.params.id);
  }
};
</script>