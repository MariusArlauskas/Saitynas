<template>
  <div style="height: 100%">
    <v-card style="height: 100%; overflow: hidden">
      <v-toolbar color="blue" dark>
        <v-toolbar-title>{{ currentUser['username'] }}</v-toolbar-title>
      </v-toolbar>
      <v-container style="height: 15%" class="ml-0 mr-5">
        <v-row>
          <v-col cols="8" class="pt-0 pb-0">
            <v-row>
              <v-col cols="5">
                <v-content class="title ml-0">Username:</v-content>
              </v-col>
              <v-col cols="7">
                <v-content class="font-weight-black title">{{ currentUser["username"] }}</v-content>
              </v-col>
            </v-row>
          </v-col>
          <v-col cols="4">
            <v-btn @click.prevent="openEditModal" color="primary" icon>
              <v-icon>edit</v-icon>
            </v-btn>
            <v-btn @click.prevent="deleteMovie" cols="1" icon color="pink">
              <v-icon>delete</v-icon>
            </v-btn>
          </v-col>
        </v-row>
        <v-row>
          <v-col cols="9" class="pt-0 pb-0">
            <v-row>
              <v-col cols="4" class="pt-0 pb-0">
                <v-content class="title">Email:</v-content>
              </v-col>
              <v-col cols="7" class="pt-1">
                <v-content>{{ currentUser['email'] }}</v-content>
              </v-col>
            </v-row>
          </v-col>
          <v-col cols="3"></v-col>
        </v-row>
      </v-container>
      <v-content class="pl-5 pt-10 title">Movie list</v-content>
      <v-divider></v-divider>

      <v-list two-line style="height: calc(100% - 128px); overflow-y: scroll">
        <template v-for="(userMovie, key) in USER_MOVIES">
          <UserMovie v-bind:key="key" :userMovie="userMovie"/>
        </template>
      </v-list>
    </v-card>
    <router-view name="userMovieInfo"></router-view>
  </div>
</template>

<script>
import UserMovie from "./UserMovie";
export default {
  name: "userMovies",
  components: { UserMovie },
  data: () => ({}),
  computed: {
    currentUser() {
      return this.$store.getters.USER(this.$route.params.id);
    },
    USER_MOVIES() {
      return this.$store.getters.USER_MOVIES(this.$route.params.id);
    }
  },
  async mounted() {
    await this.$store.dispatch("GET_USER_MOVIES", this.$route.params.id);
  }
};
</script>