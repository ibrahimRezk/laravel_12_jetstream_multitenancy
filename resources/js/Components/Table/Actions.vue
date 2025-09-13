<script setup>
import { Link } from "@inertiajs/vue3";
import Edit from "@/Components/Icons/Edit.vue";
import Trash from "@/Components/Icons/Trash.vue";

defineProps({
    editLink: {
        type: String,
        default: () => "",
    },
    showEdit: {
        type: Boolean,
        default: () => true,
    },
    showEditModal: {
        type: Boolean,
        default: () => false,
    },
    showDelete: {
        type: Boolean,
        default: () => true,
    },
});
</script>

<template>
    <div class="flex items-center space-x-">
        <slot name="button" />
        <slot name="icon" />
        <button v-if="showEdit" class="text-yellow-600 mx-2">
            <div v-if="showEditModal" @click="$emit('editClicked', $event)">
                <Edit class="w-4 h-4" />
            </div>
            <div v-else>
                <Link :href="editLink">
                    <Edit class="w-4 h-4" />
                </Link>
            </div>
        </button>

        <button v-else class="rtl:ml-8 ltr:mr-8">
            <!-- to keep spaces equal if icon disabled -->
        </button>

        <button
            v-if="showDelete"
            class="text-red-700"
            @click="$emit('deleteClicked', $event)"
        >
            <Trash class="w-4 h-4" />
        </button>
        <button v-else class="rtl:mr-8 ltr:ml-8">
            <!-- to keep spaces equal if icon disabled -->
        </button>
    </div>
</template>
