<template>
  <v-layout row justify-center>
    <v-dialog v-model="open" scrollable max-width="60%">
      <v-card>
        <v-card-title>
          <span>{{ this.$route.params.genre.name }}</span>
        </v-card-title>

        <v-card-text>
          <v-container style="height: 40%; width: 94%" class="ml-5 mr-5">
            <v-row style="width: 98%; max-height: 77%; height: 77%">
              {{ this.$route.params.genre.description }}
            </v-row>
          </v-container>
        </v-card-text>

        <v-card-actions>
          <v-btn text @click.stop="open = false" color="primary">Close</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </v-layout>
</template>

<script>
export default {
  name: "movieGenreInfoModal",
  data: () => ({
    open: true,

    title: '',
    description: "",
    rules: {
      required: value => !!value || "Required"
    }
  }),
  computed: {
  },
  methods: {
  },
  watch: {
    open: function(value) {
      if (value == false) {
        this.$router.push({
          name: "movieGenres",
          params: {
            id: this.$route.params.id
          }
        });
      }
    }
  }
};
</script>