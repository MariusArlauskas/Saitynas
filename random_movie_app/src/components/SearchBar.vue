<template>
  <input
    type="text"
    placeholder="Search..."
    v-model.trim="input"
    v-on:input="search()"
    ref="input"
    @blur="closeSearchBar()"
  />
</template>

<script>
export default {
//   name: "listSearch",
  data: () => ({
    input: ""
  }),
  methods: {
    search() {
      console.log(this.input);
    },
    closeSearchBar() {
        this.$store.commit("SET_DISPLAY_SEARCH_LIST", false);
    }
  }
};
</script>