<template>
  <v-navigation-drawer permanent style="width: 100%; height: 97%">
    <v-content style="height: 100%">
      <v-list style="height: 95.8%; overflow-y: scroll">
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
import { mapGetters } from "vuex";
export default {
  name: "users",
  components: {  },
  data: () => ({}),
  computed: {
    ...mapGetters([ "USERS"]),
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