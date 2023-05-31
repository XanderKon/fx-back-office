<script setup lang="ts">
import { ref, watch } from 'vue'
import type { Ref } from 'vue'

const currencies = ref([])
const from: Ref<any> = ref('EUR')
const to: Ref<any> =  ref('USD')

const amountFrom = ref(10)
const amountTo = ref(10)

const manual = ref(false)

interface Route {
  from: string,
  to: string,
  rate: number
}

const routes: Ref<Route[]> = ref([])

fetch(`${import.meta.env.VITE_API_URL}/rate`)
  .then((response) => response.json())
  .then((data) => (currencies.value = data))

watch(
  [from, to, amountFrom, amountTo],
  async (
    [newFrom, newTo, newAmountFrom, newAmountTo],
    [oldFrom, oldTo, oldAmountFrom, oldAmountTo]
  ) => {
    if (
      manual.value ||
      newAmountFrom.toString() === '' ||
      newAmountTo.toString() === '' ||
      newAmountFrom < 0 ||
      newAmountTo < 0
    ) {
      manual.value = false
      return false
    }
    // Define where to write
    let whereToWrite = amountTo
    if ((newAmountTo !== oldAmountTo || newTo !== oldTo) && oldTo) {
      whereToWrite.value = amountFrom.value
    }

    fetch(`${import.meta.env.VITE_API_URL}/rate`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json;charset=utf-8'
      },
      body: JSON.stringify({
        from: newAmountFrom !== oldAmountFrom || newFrom !== oldFrom || !oldFrom ? newFrom : newTo,
        to: (newAmountTo !== oldAmountTo || newTo !== oldTo) && oldTo ? newFrom : newTo,
        amount:
          newAmountFrom !== oldAmountFrom || newFrom !== oldFrom || !oldFrom
            ? newAmountFrom
            : newAmountTo
      })
    })
      .then((response) => response.json())
      .then((data) => {
        whereToWrite.value = parseFloat(data.amount.toFixed(4))
        routes.value = data.route
        manual.value = true
      })
  },
  { immediate: true }
)
</script>

<template>
  <div class="exchanger">
    <v-sheet
      class="d-flex align-center justify-center flex-wrap text-center mx-auto pa-4"
      elevation="4"
      height="100%"
      rounded
      max-width="800"
      width="100%"
    >
      <v-form fast-fail @submit.prevent class="exchanger__form">
        <v-autocomplete
          v-model="from"
          label="У меня есть"
          :items="currencies"
          persistent-hint
          return-object
          variant="outlined"
        ></v-autocomplete>

        <v-autocomplete
          v-model="to"
          label="Хочу приобрести"
          :items="currencies"
          persistent-hint
          return-object
          variant="outlined"
        ></v-autocomplete>

        <v-row>
          <v-col>
            <v-text-field
              v-model.number="amountFrom"
              type="number"
              :min="0"
              :label="from"
              variant="outlined"
              :disabled="!from"
            >
            </v-text-field>
          </v-col>
          <v-col>
            <v-text-field
              v-model.number="amountTo"
              type="number"
              :min="0"
              :label="to"
              variant="outlined"
              :disabled="!to"
            >
            </v-text-field>
          </v-col>
        </v-row>
      </v-form>
      <div class="exchanger__route" v-show="Object.keys(routes).length">
        <div class="route__item" v-for="(route, index) in routes" :key="index">
          <span
            >{{ index !== 0 ? '&nbsp;|&nbsp;' : '' }}{{ route.from }}/{{ route.to }} =
            {{ route.rate.toFixed(route.rate < 0.0001 ? 7 : 4) }}</span
          >
        </div>
      </div>
    </v-sheet>
  </div>
</template>

<style lang="scss" scoped>
.exchanger {
  height: 100%;
  display: flex;
  align-content: center;

  &__form {
    width: 100%;
  }

  &__route {
    width: 100%;
    display: inline-flex;
    font-size: 10px;
    color: var(--vt-c-divider-dark-2);
  }
}
</style>
