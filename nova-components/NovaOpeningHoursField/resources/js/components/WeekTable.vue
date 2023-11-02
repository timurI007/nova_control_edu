<template>
    <table class="openingHours weekTable table-default w-full">
        <thead class="bg-gray-50 dark:bg-gray-800">
            <tr>
                <table-header :colspan="editable ? 3 : 2">
                    {{ __('Week') }}
                </table-header>
            </tr>
        </thead>
        <tbody>
            <tr v-for="day in week" :key="day.day" class="group">
                <table-column v-bind:class="getClass(day, editable)">
                    {{ __(capitalizeFirstLetter(day.day)) }}
                </table-column>
                <table-column v-bind:class="getClass(day, editable)">
                    <div v-if="Object.values(day.intervals).length">
                        <div v-for="(interval, index) in day.intervals" :key="interval.key">
                            <div v-if="editable">
                                <interval-input
                                    :interval-prop="interval.interval"
                                    :use-text-inputs="useTextInputs"
                                    @updateInterval="$emit('updateInterval', 'week', day.day, index, $event)"
                                    @removeInterval="$emit('removeInterval', 'week', day.day, index)"
                                />
                            </div>
                            <div v-else>{{ interval.interval }}</div>
                        </div>
                    </div>
                    <div
                        v-else
                        :class="{'closed': editable}"
                    >
                        {{ __('Day Off') }}
                    </div>
                </table-column>
                <table-column v-if="editable" class="text-right">
                    <default-button @click.prevent="$emit('addInterval', 'week', day.day)"><span class="px-1">+</span></default-button>
                    <span v-if="Object.values(day.intervals).length" class="ml-2">
                        <danger-button @click.prevent="$emit('removeAllIntervals', 'week', day.day)"><span class="px-1">-</span></danger-button>
                    </span>
                </table-column>
            </tr>
        </tbody>
    </table>
</template>

<script>
import IntervalInput from "./IntervalInput";
import TableColumn from "./TableColumn";
import TableHeader from "./TableHeader";
import {editableProp, useTextInputsProp, weekProp} from "../src/props";
import {capitalizeFirstLetter} from "../src/func";
import DangerButton from "./MYUI/DangerButton.vue";

export default {
    components: { IntervalInput, TableColumn, TableHeader, DangerButton },

    props: {
        ...weekProp,
        ...editableProp,
        ...useTextInputsProp,
    },

    emits: ['updateInterval', 'removeInterval', 'addInterval', 'removeAllIntervals'],

    data() {
        return {
            current_day: (new Date()).getDay(),
            weekDays: ["sunday","monday","tuesday","wednesday","thursday","friday","saturday"]
        }
    },

    methods: {
        capitalizeFirstLetter,
        getClass(day, is_edit_page){
            const is_today = day.day == this.weekDays[this.current_day] && !is_edit_page
            return {
                'bg-gray-200 dark:bg-gray-950': is_today,
                'dark:bg-gray-800 group-hover:bg-gray-50 dark:group-hover:bg-gray-900': !is_today
            }
        }
    },
}
</script>
