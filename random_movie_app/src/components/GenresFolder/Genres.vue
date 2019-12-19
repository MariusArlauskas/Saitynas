<template>
  <v-navigation-drawer permanent style="width: 100%; height: 97%">
    <v-content style="height: 100%; overflow-y: hidden">
      <v-list>
        <v-list-item color="blue" @click.prevent="openNewListForm()" v-if="!isOpen()">
          <v-list-item-content>New Genre</v-list-item-content>

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
      <v-list v-if="!openNewListFormValue" style="height: 87.3%; overflow-y: scroll">
        <v-list-item
          :to="{ name: 'genres', params: { id: list.id } }"
          v-for="(list, key) in LISTS"
          v-bind:key="key"
        >
          <v-list-item-content>
            <v-list-item-title>{{ list.name }}</v-list-item-title>
          </v-list-item-content>
          <v-list-item-action>
            <v-list-item-title>{{ list.movies_count }}</v-list-item-title>
          </v-list-item-action>
        </v-list-item>
      </v-list>

      <v-list v-if="openNewListFormValue" style="height: 69%; overflow-y: scroll">
        <v-list-item
          :to="{ name: 'genres', params: { id: list.id } }"
          v-for="(list, key) in LISTS"
          v-bind:key="key"
        >
          <v-list-item-content>
            <v-list-item-title>{{ list.name }}</v-list-item-title>
          </v-list-item-content>
          <v-list-item-action>
            <v-list-item-title>{{ list.movies_count }}</v-list-item-title>
          </v-list-item-action>
        </v-list-item>
      </v-list>
    </v-content>
  </v-navigation-drawer>
</template>

<script>
import NewGenre from "./NewGenre";
import { mapGetters } from "vuex";
export default {
  name: "genres",
  components: { NewGenre },
  data: () => ({}),
  computed: {
    ...mapGetters(["LISTS"]),
    openNewListFormValue: {
      get() {
        return this.$store.getters.NEW_LIST_FORM;
      },
      set(value) {
        this.$store.commit("SET_NEW_LIST_FORM", value);
      }
    }
  },
  methods: {
    openNewListForm() {
      this.$store.commit("SET_NEW_LIST_FORM", true);
    },
    isOpen() {
      return this.$store.getters.NEW_LIST_FORM;
    }
  },
  mounted() {
    this.$store.dispatch("GET_LISTS");
  }
};
</script>