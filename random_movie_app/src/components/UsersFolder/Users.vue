<template>
  <v-navigation-drawer permanent style="width: 100%; height: 97%">
    <v-content style="height: 100%">
      <v-list>
        <v-list-item color="blue" @click.prevent="openNewUserForm()" v-if="!isOpen()">
          <v-list-item-content>New User</v-list-item-content>

          <v-list-item-action class="mt-0 mb-0">
            <v-list-item-icon>
              <v-icon>add</v-icon>
            </v-list-item-icon>
          </v-list-item-action>
        </v-list-item>

        <v-list-item v-if="openNewListFormValue">
          <NewGenre />
        </v-list-item>
      </v-list>
      <v-divider></v-divider>
      <v-list style="height: calc(100% - 128px); overflow-y: scroll">
        <v-list-item
          :to="{ name: 'userMovies', params: { id: user.id } }"
          v-for="(user, key) in USERS"
          v-bind:key="key"
        >
          <v-list-item-content>
            <v-list-item-title>{{ user.username }}</v-list-item-title>
          </v-list-item-content>
          <v-list-item-action>
            <v-list-item-title>{{ user.favorites_count }}</v-list-item-title>
          </v-list-item-action>
        </v-list-item>
      </v-list>
    </v-content>
  </v-navigation-drawer>
</template>

<script>
import NewGenre from "../GenresFolder/NewGenre";
import { mapGetters } from "vuex";
export default {
  name: "users",
  components: { NewGenre },
  data: () => ({}),
  computed: {
    ...mapGetters(["DISPLAY_SEARCH_USER", "USERS"]),
    openNewListFormValue: {
      get() {
        return this.$store.getters.NEW_USER_FORM;
      },
      set(value) {
        this.$store.commit("SET_NEW_USER_FORM", value);
      }
    }
  },
  methods: {
    toggleSearchUsers() {
      this.$store.commit("SET_DISPLAY_SEARCH_USER", !this.DISPLAY_SEARCH_USER);
    },
    openNewUserForm() {
      this.$store.commit("SET_NEW_USER_FORM", true);
    },
    isOpen() {
      return this.$store.getters.NEW_USER_FORM;
    }
  },
  mounted() {
    this.$store.dispatch("GET_USERS");
  }
};
</script>