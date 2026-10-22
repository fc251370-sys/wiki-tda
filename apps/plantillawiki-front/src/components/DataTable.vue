<template>
  <div>
    <v-data-table
      :headers="headers"
      :items="items"
      :no-data-text="noData"
      hide-default-footer
    >
      <template v-for="(_, name) in $slots" v-slot:[name]="slotProps">
        <slot :name="name" v-bind="slotProps" />
      </template>
    </v-data-table>
    <Pagination :total-page="totalPage" :page="page" @update:page="onPageChange"></Pagination>
  </div>
</template>
<script setup>
import Pagination from "./Pagination.vue";
import { defineProps, defineEmits } from "vue";

const emit = defineEmits(['update:page']);

const onPageChange = (newPage) => {
  emit('update:page', newPage);
};

const props = defineProps({
  headers: {
    type: Array,
    default: [],
  },
  items: {
    type: Array,
    default: [],
  },
  noData: {
    type: String,
    default: "No hay datos",
  },
  totalPage:{
    type:Number,
    default:1
  },
  page:{
    type:Number,
    default:1
  },
  verAcciones:{
    type:Boolean,
    default:true
  }
});
</script>
