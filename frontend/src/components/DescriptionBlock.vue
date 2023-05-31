<script setup lang="ts"></script>

<template>
  <div class="greetings">
    <div class="greetings__logo">
      <img alt="Exchange" class="logo" src="@/assets/logo.svg" width="125" height="125" />
    </div>
    <h1 class="green">Конвертер валют</h1>
    <h3>
      Механизм работы основан на алгоритме
      <a
        href="https://ru.wikipedia.org/wiki/%D0%9F%D0%BE%D0%B8%D1%81%D0%BA_%D0%B2_%D1%88%D0%B8%D1%80%D0%B8%D0%BD%D1%83"
        target="_blank"
      >
        BFS</a
      >.<br />Котировки для приложения поставляются ресурсами:
    </h3>
    <ol class="has-padding">
      <li>
        <a href="https://www.ecb.europa.eu/stats/eurofxref/eurofxref-daily.xml" target="_blank">
          https://www.ecb.europa.eu/stats/eurofxref/eurofxref-daily.xml</a
        >
      </li>
      <li>
        <a href="https://api.coindesk.com/v1/bpi/currentprice.json" target="_blank">
          https://api.coindesk.com/v1/bpi/currentprice.json</a
        >
      </li>
    </ol>
    <p class="greetings__build_by">
      build by
      <a href="https://vitejs.dev/" target="_blank" rel="noopener">Vite</a> +
      <a href="https://vuejs.org/" target="_blank" rel="noopener">Vue 3</a>.
    </p>
    <p class="greetings__github">
      Developed by Xander
      <a href="https://github.com/XanderKon/fx-back-office" target="_blank">
        <img
          alt="Github"
          class="greetings__github-icon"
          src="@/assets/github-mark.svg"
          width="18"
          height="18"
        />
      </a>
    </p>
  </div>
</template>

<style lang="scss" scoped>
.greetings {
  &__build_by,
  &__github {
    margin-top: 15px;
    font-size: 10px;
    color: var(--vt-c-divider-dark-2);
  }

  &__github {
    display: inline-flex;
    align-content: center;

    a:hover {
      background: none;
    }
  }

  &__github-icon {
    margin-left: 7px;
  }
}

ol {
  &.has-padding {
    margin: 5px 0 5px 30px;
  }
}

h1 {
  font-weight: 500;
  font-size: 2.6rem;
  top: -10px;
}

h3 {
  font-size: 1.2rem;
}

.greetings h1,
.greetings h3 {
  text-align: center;
}

@media (min-width: 1024px) {
  .greetings h1,
  .greetings h3 {
    text-align: left;
  }
}
</style>
<script setup></script>
