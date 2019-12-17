<template>
  <v-layout row justify-center>
    <v-dialog v-model="open" scrollable="" max-width="60%">
      <v-card>
        <v-card-title>
          <span>Task title</span>
        </v-card-title>

        <v-card-text>
          <v-container>
            <v-form>
              <v-textarea
                outlined
                v-model="note"
                label="Note"
                placeholder="Start writting your note here"
              ></v-textarea>

              <v-btn color="success">Save</v-btn>
            </v-form>
          </v-container>

          <v-list three-line subheader>
              <v-subheader>Notes</v-subheader>

              <v-list-item
                v-for="(note, key) in notes"
                v-bind:key="key"
              >
                  <v-list-item-content>
                      <v-list-item-title>{{ note.note }}</v-list-item-title>
                  </v-list-item-content>

                  <v-list-item-action>
                      <v-btn icon>
                          <v-icon color="red">delete</v-icon>
                      </v-btn>
                  </v-list-item-action>
              </v-list-item>
          </v-list>
        </v-card-text>

        <v-card-actions>
            <v-btn text color="primary">Close</v-btn>
            <v-spacer></v-spacer>
            <v-btn text color="red" dark>Remove task</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </v-layout>
</template>

<script>
export default {
  name: "notesModal",
  data: () => ({
    open: true,
    note: "",
    notes: []
  })
};
</script>