<template>
  <div>
    <v-card height="100%">
      <v-toolbar color="pink" dark>
        <v-toolbar-title>Profile options</v-toolbar-title>
      </v-toolbar>

      <v-list>
        <v-list-item @click.prevent="displayNotification()">
          <v-list-item-content>
            <v-list-item-title class="danger">Change username</v-list-item-title>
          </v-list-item-content>
        </v-list-item>

        <v-list-item @click.prevent="openDrawer()">
          <v-list-item-content>
            <v-list-item-title class="danger">Open profile</v-list-item-title>
          </v-list-item-content>
        </v-list-item>
      </v-list>
    </v-card>
    <MoreOptions />
  </div>
</template>

<script>
import MoreOptions from "./MoreOptions";
export default {
  name: "optionsBar",
  components: { MoreOptions },
  data: () => ({
    items: [
      {
        action: "sort",
        title: "Sort by",
        active: true,
        items: [
          {
            title: "Date",
            by: "date"
          },
          {
            title: "Name",
            by: "name"
          }
        ]
      },
      {
        action: "filter_list",
        title: "Filter by",
        active: false,
        items: [
          {
            title: "Remaining",
            by: "remaining"
          },
          {
            title: "Completed",
            by: "completed"
          },
          {
            title: "All",
            by: "all"
          }
        ]
      }
    ]
  }),
  computed: {},
  methods: {
    sort(value) {
      console.log("Sort by " + value);
    },
    filter(value) {
      console.log("Filter by " + value);
    },
    openDrawer() {
      this.$store.commit("SET_DRAWER", true);
    },
    displayNotification() {
      this.$store.commit("SET_NOTIFICATION", {
        display: true,
        alertClass: "success",
        timeout: 3000,
        text: "Username changed! (not implemented)"
      });
    }
  }
};
</script>