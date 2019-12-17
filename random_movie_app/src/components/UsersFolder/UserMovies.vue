<template>
  <div style="height: 100%">
    <v-card style="height: 100%; overflow: hidden">
      <v-toolbar color="blue" dark>
        <v-toolbar-title>{{ userTitle }}</v-toolbar-title>

        <v-spacer></v-spacer>

        <v-btn icon>
          <v-icon>search</v-icon>
        </v-btn>
      </v-toolbar>

      <v-list two-line style="height: calc(100% - 128px); overflow-y: scroll">
        <template v-for="(userMovie, key) in USER_MOVIES">
          <UserMovie v-bind:key="key" :userMovie="userMovie" :index="key" />
        </template>
      </v-list>
      <v-divider></v-divider>
      <v-card-actions>
        <v-layout>
          <v-flex>
            <NewTask />
          </v-flex>
        </v-layout>
      </v-card-actions>
    </v-card>
    <router-view :key="$route.fullPath" name="userMovieNotes"></router-view>
  </div>
</template>

<script>
import UserMovie from "./UserMovie";
import NewTask from "../NewTask";
export default {
  name: "userMovies",
  components: { UserMovie, NewTask},
  data: () => ({}),
  computed: {
    userTitle() {
      return this.$store.getters.USER_TITLE(this.$route.params.id);
    },
    USER_MOVIES() {
      return this.$store.getters.USER_MOVIES(this.$route.params.id);
    }
  },
  async mounted(){
    await this.$store.dispatch("GET_USER_MOVIES", this.$route.params.id);
  }
};
</script>