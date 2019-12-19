<template>
  <div style="height: 100%">
    <v-card style="height: 100%; overflow: hidden">
      <v-toolbar color="blue" dark>
        <v-toolbar-title>{{ currentGenre["name"] }}</v-toolbar-title>
      </v-toolbar>

      <v-container style="height: 30%" class="ml-5 mr-5">
        <v-row>
          <v-col cols="7">
            <v-row>
              <v-content class="font-weight-black">{{ currentGenre["name"] }}</v-content>
            </v-row>
          </v-col>
          <v-col cols="1">
            <v-btn @click.prevent="openEditModal" color="primary" icon>
              <v-icon>edit</v-icon>
            </v-btn>
          </v-col>
          <v-col cols="1">
            <v-btn @click.prevent="deleteGenre" cols="1" icon color="pink">
              <v-icon>delete</v-icon>
            </v-btn>
          </v-col>
        </v-row>
        <v-row
          style="width: 98%; max-height: 70%; height: 70%; overflow-y: scroll"
        >{{ currentGenre["description"] }}</v-row>
      </v-container>
      <v-content class="pl-5 title">Movie list</v-content>
      <v-divider></v-divider>
      <v-list two-line style="height: 59%; overflow-y: scroll">
        <template v-for="(task, key) in TASKS">
          <Task v-bind:key="key" :task="task"/>
        </template>
      </v-list>
    </v-card>
    <router-view name="genresEdit"></router-view>
    <router-view name="genreMovieInfoModal"></router-view>
  </div>
</template>

<script>
import Task from "./Task";
export default {
  name: "tasks",
  components: { Task },
  data: () => ({}),
  methods: {
    openEditModal() {
      this.$router.push({
        name: "genresEdit"
      });
    },
    async deleteGenre() {
      await this.$store
        .dispatch("DELETE_GENRE", {
          genreId: this.$route.params.id
        })
        .then(() => {
          this.$store.commit("SET_NOTIFICATION", {
            display: true,
            text: "Genre has been removed!",
            alertClass: "warning"
          })
          this.$store.dispatch("GET_LISTS")
          this.$router.push({
            name: "todo"
          });
        })
    }
  },
  computed: {
    currentGenre() {
      return this.$store.getters.LIST(this.$route.params.id);
    },
    TASKS() {
      return this.$store.getters.TASKS(this.$route.params.id);
    }
  },
  async mounted() {
    await this.$store.dispatch("GET_TASKS", this.$route.params.id);
  }
};
</script>