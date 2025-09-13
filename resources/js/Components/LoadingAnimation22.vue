<script setup>
import { computed, onMounted, onUnmounted, watch } from "vue";

const props = defineProps({
    show: {
        type: Boolean,
        default: false,
    },
});

watch(
    () => props.show,
    () => {
        if (props.show) {
            document.body.style.overflow = "hidden";
        } else {
            document.body.style.overflow = null;
        }
    }
);
</script>

<template>
    <teleport to="body">
        <transition leave-active-class="duration-200">
            <div
                v-show="show"
                class="fixed inset-0 overflow-y-auto px-4 py-6 sm:px-0 z-50 flex justify-center items-center"
                scroll-region
            >
                <transition
                    enter-active-class="ease-out duration-300"
                    enter-from-class="opacity-0"
                    enter-to-class="opacity-100"
                    leave-active-class="ease-in duration-200"
                    leave-from-class="opacity-100"
                    leave-to-class="opacity-0"
                >
                    <div
                        v-show="show"
                        class="fixed inset-0 transform transition-all"
                    >
                        <div
                            class="absolute inset-0 bg-black opacity-30"
                        />
                    </div>
                </transition>

                <transition
                    enter-active-class="ease-out duration-300"
                    enter-from-class="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    enter-to-class="opacity-100 translate-y-0 sm:scale-100"
                    leave-active-class="ease-in duration-200"
                    leave-from-class="opacity-100 translate-y-0 sm:scale-100"
                    leave-to-class="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                >
                <svg
                xmlns="http://www.w3.org/2000/svg"
                xmlns:xlink="http://www.w3.org/1999/xlink"
                style="margin: auto; display: block"
                width="200px"
                height="100px"
                viewBox="0 0 100 100"
                preserveAspectRatio="xMidYMid"
                class=""
              >
                <g transform="translate(20 50)">
                  <circle cx="0" cy="0" r="10" fill="#e15b64">
                    <animateTransform
                      attributeName="transform"
                      type="scale"
                      begin="-0.375s"
                      calcMode="spline"
                      keySplines="0.3 0 0.7 1;0.3 0 0.7 1"
                      values="0;1;0"
                      keyTimes="0;0.5;1"
                      dur="1s"
                      repeatCount="indefinite"
                    ></animateTransform>
                  </circle>
                </g>
                <g transform="translate(40 50)">
                  <circle cx="0" cy="0" r="10" fill="#f8b26a">
                    <animateTransform
                      attributeName="transform"
                      type="scale"
                      begin="-0.25s"
                      calcMode="spline"
                      keySplines="0.3 0 0.7 1;0.3 0 0.7 1"
                      values="0;1;0"
                      keyTimes="0;0.5;1"
                      dur="1s"
                      repeatCount="indefinite"
                    ></animateTransform>
                  </circle>
                </g>
                <g transform="translate(60 50)">
                  <circle cx="0" cy="0" r="10" fill="#abbd81">
                    <animateTransform
                      attributeName="transform"
                      type="scale"
                      begin="-0.125s"
                      calcMode="spline"
                      keySplines="0.3 0 0.7 1;0.3 0 0.7 1"
                      values="0;1;0"
                      keyTimes="0;0.5;1"
                      dur="1s"
                      repeatCount="indefinite"
                    ></animateTransform>
                  </circle>
                </g>
                <g transform="translate(80 50)">
                  <circle cx="0" cy="0" r="10" fill="#81a3bd">
                    <animateTransform
                      attributeName="transform"
                      type="scale"
                      begin="0s"
                      calcMode="spline"
                      keySplines="0.3 0 0.7 1;0.3 0 0.7 1"
                      values="0;1;0"
                      keyTimes="0;0.5;1"
                      dur="1s"
                      repeatCount="indefinite"
                    ></animateTransform>
                  </circle>
                </g>
              </svg>
                </transition>
            </div>
        </transition>
    </teleport>
</template>
