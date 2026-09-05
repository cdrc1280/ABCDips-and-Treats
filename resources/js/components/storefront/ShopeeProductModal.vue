<template>
    <Teleport to="body">
        <Transition enter-active-class="transition-all duration-300 ease-out" enter-from-class="opacity-0"
            enter-to-class="opacity-100" leave-active-class="transition-all duration-200 ease-in"
            leave-from-class="opacity-100" leave-to-class="opacity-0">
            <div v-if="modalStore.isOpen && modalStore.product"
                class="fixed inset-0 z-50 overflow-y-auto bg-ink/60 backdrop-blur-sm flex items-center justify-center p-2 sm:p-4 md:p-6"
                @click.self="modalStore.closeModal">
                <Transition enter-active-class="transition-all duration-300 ease-out"
                    enter-from-class="opacity-0 scale-95 translate-y-4"
                    enter-to-class="opacity-100 scale-100 translate-y-0"
                    leave-active-class="transition-all duration-200 ease-in"
                    leave-from-class="opacity-100 scale-100 translate-y-0"
                    leave-to-class="opacity-0 scale-95 translate-y-4">
                    <div v-if="modalStore.isOpen && modalStore.product"
                        class="bg-surface rounded-2xl sm:rounded-3xl shadow-2xl border border-brand-caramel/30 overflow-hidden w-full max-w-5xl relative max-h-[92vh] flex flex-col text-ink">
                        <!-- CLOSE -->
                        <button type="button"
                            class="absolute top-3 right-3 z-30 w-9 h-9 rounded-full bg-white/90 hover:bg-white text-brand-choco shadow-sm flex items-center justify-center transition-all cursor-pointer border border-brand-caramel/20 font-bold"
                            @click="modalStore.closeModal"><X class="w-4 h-4" /></button>

                        <div class="overflow-y-auto p-4 sm:p-6 md:p-8 space-y-6">
                            <!-- BREADCRUMB -->
                            <nav class="flex items-center gap-1.5 text-xs text-warm-gray flex-wrap pb-1 font-semibold">
                                <span class="hover:text-brand-choco cursor-pointer" @click="$router.push('/')">
                                    Home
                                </span>

                                <span>/</span>

                                <span class="hover:text-brand-choco cursor-pointer" @click="$router.push('/shop')">
                                    Shop
                                </span>

                                <template v-if="modalStore.product.category?.name">
                                    <span>/</span>

                                    <span class="text-brand-choco font-bold">
                                        {{ modalStore.product.category.name }}
                                    </span>
                                </template>

                                <span>/</span>

                                <span class="text-brand-choco font-bold truncate max-w-xs">
                                    {{ modalStore.product.name }}
                                </span>
                            </nav>

                            <!-- PRODUCT -->
                            <div
                                class="bg-white rounded-2xl p-4 sm:p-6 shadow-sm border border-brand-caramel/20 grid grid-cols-1 md:grid-cols-12 gap-6 md:gap-8 items-start">
                                <!-- ========================= -->
                                <!-- IMAGE -->
                                <!-- ========================= -->
                                <div class="md:col-span-5 space-y-4">
                                    <div class="relative aspect-square rounded-2xl overflow-hidden bg-surface/50 border border-brand-caramel/20 select-none"
                                        @touchstart="onTouchStart" @touchmove="onTouchMove" @touchend="onTouchEnd">
                                        <!-- IMAGE CAROUSEL -->
                                        <div v-if="displayImages.length"
                                            class="flex h-full transition-transform duration-300 ease-out" :style="{
                                                transform: `translateX(-${activeIndex *
                                                    (100 /
                                                        displayImages.length)
                                                    }%)`,
                                                width: `${displayImages.length * 100
                                                    }%`,
                                            }">
                                            <div v-for="(
img, idx
                                                ) in displayImages" :key="`${img}-${idx}`" class="h-full shrink-0"
                                                :style="{
                                                    width: `${100 /
                                                        displayImages.length
                                                        }%`,
                                                }">
                                                <img :src="img" :alt="`${modalStore.product.name} - Image ${idx + 1
                                                    }`" class="w-full h-full object-cover object-center"
                                                    loading="eager" decoding="async" @error="
                                                        handleImageError(
                                                            img
                                                        )
                                                        " />
                                            </div>
                                        </div>

                                        <!-- NO IMAGE -->
                                        <div v-else
                                            class="w-full h-full flex flex-col items-center justify-center text-warm-gray text-sm gap-2">
                                            <div
                                                class="w-16 h-16 rounded-full bg-brand-tan/20 flex items-center justify-center text-3xl">
                                                
                                            </div>

                                            <span>
                                                No image available
                                            </span>
                                        </div>

                                        <!-- LEFT ARROW -->
                                        <button v-if="
                                            displayImages.length > 1 &&
                                            activeIndex > 0
                                        " type="button"
                                            class="absolute left-2 top-1/2 -translate-y-1/2 z-20 w-9 h-9 rounded-full bg-black/40 hover:bg-black/60 text-white flex items-center justify-center"
                                            @click="prevImage">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                                    d="M15 19l-7-7 7-7" />
                                            </svg>
                                        </button>

                                        <!-- RIGHT ARROW -->
                                        <button v-if="
                                            displayImages.length > 1 &&
                                            activeIndex <
                                            displayImages.length - 1
                                        " type="button"
                                            class="absolute right-2 top-1/2 -translate-y-1/2 z-20 w-9 h-9 rounded-full bg-black/40 hover:bg-black/60 text-white flex items-center justify-center"
                                            @click="nextImage">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                                    d="M9 5l7 7-7 7" />
                                            </svg>
                                        </button>

                                        <!-- DOTS -->
                                        <div v-if="displayImages.length > 1"
                                            class="absolute bottom-3 left-1/2 -translate-x-1/2 flex gap-1.5 z-20">
                                            <button v-for="(
_, idx
                                                ) in displayImages" :key="idx" type="button"
                                                class="rounded-full transition-all" :class="activeIndex === idx
                                                        ? 'w-5 h-2 bg-white'
                                                        : 'w-2 h-2 bg-white/50'
                                                    " @click="activeIndex = idx" />
                                        </div>

                                        <!-- MAIN BADGE -->
                                        <div v-if="activeIndex === 0"
                                            class="absolute top-3 right-3 z-20 bg-brand-choco/80 text-white text-[10px] font-bold px-2 py-0.5 rounded-full">
                                            Main
                                        </div>

                                        <!-- LOADING -->
                                        <div v-if="productLoading"
                                            class="absolute inset-0 z-10 bg-white/40 backdrop-blur-[1px] flex items-center justify-center">
                                            <div
                                                class="w-8 h-8 rounded-full border-2 border-brand-caramel/30 border-t-brand-choco animate-spin">
                                            </div>
                                        </div>
                                    </div>

                                    <!-- THUMBNAILS -->
                                    <div v-if="displayImages.length > 1"
                                        class="flex gap-2 overflow-x-auto no-scrollbar px-1 pb-1">
                                        <button v-for="(
img, idx
                                            ) in displayImages" :key="`${img}-thumb-${idx}`" type="button"
                                            class="w-16 h-16 rounded-xl border-2 shrink-0 overflow-hidden cursor-pointer transition-all relative"
                                            :class="activeIndex === idx
                                                    ? 'border-brand-choco scale-95 shadow-md'
                                                    : 'border-brand-caramel/20 opacity-60 hover:opacity-100'
                                                " @click="activeIndex = idx">
                                            <img :src="img" :alt="`Thumb ${idx + 1}`" class="w-full h-full object-cover"
                                                loading="lazy" decoding="async" @error="
                                                    handleImageError(img)
                                                    " />
                                        </button>
                                    </div>

                                    <!-- WISHLIST -->
                                    <div
                                        class="flex items-center justify-end pt-2 border-t border-brand-caramel/15 text-xs">
                                        <button type="button"
                                            class="flex items-center gap-1.5 cursor-pointer transition-colors font-semibold"
                                            :class="wishlistStore.isInWishlist(
                                                modalStore.product.id
                                            )
                                                    ? 'text-red-500 font-bold'
                                                    : 'text-warm-gray hover:text-red-500'
                                                " @click="
                                                wishlistStore.toggleWishlist(
                                                    modalStore.product
                                                )
                                                ">
                                            <svg class="w-4 h-4" :fill="wishlistStore.isInWishlist(
                                                modalStore.product.id
                                            )
                                                    ? 'currentColor'
                                                    : 'none'
                                                " stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                            </svg>

                                            {{
                                                wishlistStore.isInWishlist(
                                                    modalStore.product.id
                                                )
                                                    ? "Saved to Wishlist"
                                                    : "Save to Wishlist"
                                            }}
                                        </button>
                                    </div>
                                </div>

                                <!-- ========================= -->
                                <!-- RIGHT SIDE -->
                                <!-- ========================= -->
                                <div class="md:col-span-7 space-y-5">
                                    <!-- TITLE -->
                                    <div>
                                        <div class="flex items-center gap-3 mb-1">
                                            <span v-if="
                                                modalStore.product
                                                    .category?.name
                                            "
                                                class="text-xs font-bold uppercase tracking-wider text-brand-caramel">
                                                {{
                                                    modalStore.product.category
                                                        .name
                                                }}
                                            </span>

                                            <span v-if="
                                                modalStore.product
                                                    .reviews_count &&
                                                modalStore.product
                                                    .reviews_count > 0
                                            "
                                                class="inline-flex items-center gap-1 bg-surface border border-brand-caramel/30 px-2.5 py-0.5 rounded-full text-xs font-extrabold text-brand-choco">
                                                <Star class="w-3.5 h-3.5 fill-amber-400 text-amber-500 inline shrink-0" />
                                                {{
                                                    modalStore.product
                                                        .avg_rating
                                                }}

                                                <span class="text-warm-gray font-normal">
                                                    ({{
                                                        modalStore.product
                                                            .reviews_count
                                                    }}
                                                    reviews)
                                                </span>
                                            </span>
                                        </div>

                                        <div v-if="
                                            modalStore.editingCartItem
                                        "
                                            class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-brand-tan/30 border border-brand-caramel/40 text-brand-choco text-xs font-bold mb-1">
                                            Editing item in your basket
                                        </div>

                                        <h1
                                            class="text-2xl sm:text-3xl font-extrabold text-ink tracking-tight leading-snug">
                                            {{ modalStore.product.name }}
                                        </h1>
                                    </div>

                                    <!-- PRICE -->
                                    <div
                                        class="bg-surface p-4 rounded-xl border border-brand-caramel/20 flex flex-wrap items-baseline gap-3">
                                        <template v-if="hasPrice">
                                            <span class="text-3xl font-extrabold text-brand-choco">
                                                ₱{{
                                                    effectivePrice.toFixed(2)
                                                }}
                                            </span>

                                            <span v-if="
                                                modalStore.product
                                                    .is_on_sale &&
                                                !selectedVariation &&
                                                !selectedFlavor
                                            " class="text-base text-warm-gray line-through">
                                                ₱{{
                                                    Number(
                                                        modalStore.product.price
                                                    ).toFixed(2)
                                                }}
                                            </span>
                                        </template>

                                        <template v-else>
                                            <span class="text-lg font-semibold text-warm-gray italic">
                                                Price on Request
                                            </span>
                                        </template>
                                    </div>

                                    <!-- DESCRIPTION -->
                                    <p class="text-xs sm:text-sm text-warm-gray leading-relaxed">
                                        {{
                                            modalStore.product
                                                .short_description
                                        }}
                                    </p>

                                    <!-- STOCK -->
                                    <div class="grid grid-cols-2 gap-4 py-3 border-y border-brand-caramel/20">
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="w-9 h-9 rounded-xl bg-brand-tan/20 flex items-center justify-center text-brand-choco">
                                                
                                            </div>

                                            <div>
                                                <div class="text-[11px] text-warm-gray">
                                                    Baking Prep Time
                                                </div>

                                                <div class="text-xs font-bold text-ink">
                                                    {{
                                                        modalStore.product
                                                            .prep_time_minutes ||
                                                        20
                                                    }}
                                                    minutes
                                                </div>
                                            </div>
                                        </div>

                                        <div class="flex items-center gap-3">
                                            <div
                                                class="w-9 h-9 rounded-xl bg-brand-tan/20 flex items-center justify-center text-brand-choco">
                                                
                                            </div>

                                            <div>
                                                <div class="text-[11px] text-warm-gray">
                                                    Stock Status
                                                </div>

                                                <div class="text-xs font-bold" :class="modalStore.product
                                                        .is_in_stock
                                                        ? 'text-success'
                                                        : 'text-error'
                                                    ">
                                                    {{
                                                        modalStore.product
                                                            .is_in_stock
                                                            ? "In Stock"
                                                            : "Out of Stock"
                                                    }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- ALLERGENS -->
                                    <div v-if="normalizedAllergens.length" class="space-y-1.5">
                                        <span class="block text-xs font-bold uppercase tracking-wider text-brand-choco">
                                            Allergen Information
                                        </span>

                                        <div class="flex flex-wrap gap-2">
                                            <span v-for="(
alg, idx
                                                ) in normalizedAllergens" :key="idx"
                                                class="px-2.5 py-1 rounded-lg bg-warning/10 text-warning text-xs font-semibold border border-warning/20">
                                                {{ getAllergenName(alg) }}

                                                <template v-if="getAllergenType(alg)">
                                                    ({{
                                                        getAllergenType(alg)
                                                    }})
                                                </template>
                                            </span>
                                        </div>
                                    </div>

                                    <!-- ========================= -->
                                    <!-- FLAVORS -->
                                    <!-- ========================= -->
                                    <div v-if="normalizedFlavors.length" class="space-y-2.5">
                                        <div class="flex items-center justify-between">
                                            <span class="block text-xs font-bold uppercase tracking-wider text-brand-choco flex items-center gap-1.5">
                                                <span>{{ isAssorted ? 'Select Assorted Flavors' : 'Select Flavor' }}</span>
                                                <span v-if="isAssorted" class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-amber-100 text-amber-900 border border-amber-300">
                                                    Multi-Select
                                                </span>
                                                <span v-else class="text-[10px] font-normal text-warm-gray lowercase">
                                                    (optional)
                                                </span>
                                            </span>

                                            <span v-if="isAssorted" class="text-xs font-bold" :class="selectedFlavorIndexes.length > 0 ? 'text-amber-700 font-extrabold' : 'text-warm-gray'">
                                                <template v-if="maxAllowedFlavors">
                                                    {{ selectedFlavorIndexes.length }} of {{ maxAllowedFlavors }} selected
                                                </template>
                                                <template v-else>
                                                    {{ selectedFlavorIndexes.length }} selected
                                                </template>
                                            </span>
                                        </div>

                                        <div class="flex flex-wrap gap-2">
                                            <button v-for="(flv, idx) in normalizedFlavors" :key="getOptionKey(flv, idx)"
                                                type="button"
                                                class="px-3.5 py-2 rounded-xl border-2 text-xs font-bold transition-all cursor-pointer flex items-center gap-1.5 shadow-2xs"
                                                :class="isFlavorSelected(idx)
                                                        ? 'border-amber-600 bg-amber-600 text-white shadow-sm ring-2 ring-amber-400/40'
                                                        : 'border-amber-200 text-amber-900 bg-amber-50/60 hover:border-amber-400 hover:bg-amber-100/50'
                                                    "
                                                @click="toggleFlavor(idx)">
                                                <span v-if="isFlavorSelected(idx)" class="text-[11px] font-black"><Check class="w-3 h-3 inline" /></span>
                                                <span>{{ getFlavorName(flv) }}</span>

                                                <span v-if="getPriceModifier(flv) !== 0"
                                                    class="ml-0.5 px-1.5 py-0.5 rounded-md text-[10px] font-bold"
                                                    :class="isFlavorSelected(idx) ? 'bg-amber-700/80 text-amber-100' : 'bg-amber-200/80 text-amber-900'">
                                                    {{ getPriceModifier(flv) > 0 ? "+" : "" }}₱{{ getPriceModifier(flv).toFixed(2) }}
                                                </span>
                                            </button>
                                        </div>
                                    </div>

                                    <!-- STATIC FLAVOR -->
                                    <div v-else-if="
                                        modalStore.product.flavor
                                    " class="space-y-1.5">
                                        <span class="block text-xs font-bold uppercase tracking-wider text-brand-choco">
                                            Flavor Profile
                                        </span>

                                        <div
                                            class="px-3.5 py-2 rounded-xl bg-amber-50 text-amber-800 font-bold text-xs border border-amber-200">
                                            
                                            {{
                                                modalStore.product.flavor
                                            }}
                                        </div>
                                    </div>

                                    <!-- ========================= -->
                                    <!-- VARIATIONS -->
                                    <!-- ========================= -->
                                    <div v-if="normalizedVariations.length" class="space-y-2">
                                        <span class="block text-xs font-bold uppercase tracking-wider text-brand-choco">
                                            {{ variationLabel }}
                                        </span>

                                        <div class="flex flex-wrap gap-2">
                                            <button v-for="(
variation, idx
                                                ) in normalizedVariations" :key="getOptionKey(
                                                    variation,
                                                    idx
                                                )
                                                    " type="button"
                                                class="px-3.5 py-1.5 rounded-xl border-2 text-xs font-bold transition-all cursor-pointer"
                                                :class="selectedVariationIdx ===
                                                        idx
                                                        ? 'border-brand-choco bg-brand-choco text-white'
                                                        : 'border-brand-caramel/30 text-brand-choco hover:border-brand-choco'
                                                    " @click="
                                                    selectedVariationIdx =
                                                    idx
                                                    ">
                                                {{
                                                    getVariationLabel(
                                                        variation
                                                    )
                                                }}

                                                <span v-if="
                                                    getPriceModifier(
                                                        variation
                                                    ) !== 0
                                                " class="ml-1 font-normal opacity-80">
                                                    {{
                                                        getPriceModifier(
                                                            variation
                                                        ) > 0
                                                            ? "+"
                                                            : ""
                                                    }}₱{{
                                                        getPriceModifier(
                                                            variation
                                                        ).toFixed(2)
                                                    }}
                                                </span>
                                            </button>
                                        </div>
                                    </div>

                                    <!-- ========================= -->
                                    <!-- QUANTITY -->
                                    <!-- ========================= -->
                                    <div class="space-y-4 pt-1">
                                        <div class="flex items-center gap-3">
                                            <span class="text-xs text-warm-gray font-bold uppercase tracking-wider">
                                                Quantity:
                                            </span>

                                            <div
                                                class="flex items-center border border-brand-caramel/30 rounded-xl bg-white p-0.5">
                                                <button type="button"
                                                    class="w-8 h-8 rounded-lg flex items-center justify-center text-brand-choco hover:bg-surface font-extrabold text-sm disabled:opacity-30"
                                                    :disabled="quantity <= 1
                                                        " @click="
                                                        decreaseQuantity
                                                    ">
                                                    -
                                                </button>

                                                <input
                                                    type="number"
                                                    :min="1"
                                                    :max="modalStore.product?.stock_qty || 999"
                                                    :value="quantity"
                                                    @input="onQuantityInput"
                                                    @blur="onQuantityBlur"
                                                    @keydown.enter.prevent="onQuantityBlur"
                                                    class="w-12 text-center font-bold text-ink text-sm bg-transparent border-0 outline-none focus:ring-0 [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none"
                                                    inputmode="numeric"
                                                />

                                                <button type="button"
                                                    class="w-8 h-8 rounded-lg flex items-center justify-center text-brand-choco hover:bg-surface font-extrabold text-sm disabled:opacity-30"
                                                    :disabled="maxQuantityReached
                                                        " @click="
                                                        increaseQuantity
                                                    ">
                                                    +
                                                </button>
                                            </div>

                                            <span v-if="
                                                modalStore.product
                                                    .stock_qty
                                            " class="text-xs text-warm-gray">
                                                ({{
                                                    modalStore.product
                                                        .stock_qty
                                                }}
                                                available)
                                            </span>
                                        </div>

                                        <!-- ACTIONS -->
                                        <div class="flex flex-col sm:flex-row items-center gap-3">
                                            <!-- UPDATE / ADD -->
                                            <button type="button"
                                                class="w-full sm:flex-1 px-5 py-3 rounded-xl border-2 border-brand-choco bg-surface hover:bg-brand-tan/20 text-brand-choco font-bold text-sm flex items-center justify-center gap-2 transition-all cursor-pointer shadow-xs disabled:opacity-50"
                                                :disabled="actionDisabled" @click="
                                                    handleAddToCart
                                                ">
                                                <svg v-if="!adding" class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 0a2 2 0 100 4 2 2 0 000-4z" />
                                                </svg>

                                                <span v-if="adding"
                                                    class="w-4 h-4 rounded-full border-2 border-current border-t-transparent animate-spin"></span>

                                                {{
                                                    modalStore.editingCartItem
                                                        ? "Update Basket"
                                                        : "Add to Basket"
                                                }}
                                            </button>

                                            <!-- SAVE / BUY -->
                                            <button type="button"
                                                class="w-full sm:flex-1 px-6 py-3 rounded-xl bg-brand-choco hover:bg-[#442917] text-surface font-extrabold text-sm flex items-center justify-center transition-all cursor-pointer shadow-sm disabled:opacity-50"
                                                :disabled="actionDisabled" @click="
                                                    handleBuyNow
                                                ">
                                                {{
                                                    modalStore.editingCartItem
                                                        ? "Save Changes"
                                                        : "Buy Now"
                                                }}

                                                <template v-if="hasPrice">
                                                    • ₱{{
                                                        (
                                                            effectivePrice *
                                                            quantity
                                                        ).toFixed(2)
                                                    }}
                                                </template>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- DESCRIPTION -->
                            <div v-if="
                                modalStore.product.description ||
                                modalStore.product.nutrition
                            "
                                class="bg-white rounded-2xl p-4 sm:p-6 shadow-sm border border-brand-caramel/20 space-y-4">
                                <div class="flex border-b border-brand-caramel/20 gap-6">
                                    <button type="button" class="pb-3 text-sm font-bold transition-all border-b-2"
                                        :class="activeTab === 'description'
                                                ? 'border-brand-choco text-brand-choco'
                                                : 'border-transparent text-warm-gray'
                                            " @click="
                                            activeTab = 'description'
                                            ">
                                        Product Description
                                    </button>

                                    <button v-if="
                                        modalStore.product.nutrition
                                    " type="button" class="pb-3 text-sm font-bold transition-all border-b-2"
                                        :class="activeTab === 'nutrition'
                                                ? 'border-brand-choco text-brand-choco'
                                                : 'border-transparent text-warm-gray'
                                            " @click="
                                            activeTab = 'nutrition'
                                            ">
                                        Nutrition Facts
                                    </button>
                                </div>

                                <div v-if="
                                    activeTab === 'description'
                                " class="text-xs sm:text-sm text-ink/90 leading-relaxed" v-html="modalStore.product
                                            .description ||
                                        modalStore.product
                                            .short_description
                                        " />

                                <div v-else-if="
                                    activeTab === 'nutrition' &&
                                    modalStore.product.nutrition
                                ">
                                    <div class="border border-ink p-3 rounded-xl space-y-1.5 text-ink text-xs max-w-xs">
                                        <h4 class="font-extrabold text-base border-b-2 border-ink pb-1">
                                            Nutrition Facts
                                        </h4>

                                        <p class="text-[11px] font-semibold">
                                            Serving Size:
                                            {{
                                                modalStore.product
                                                    .nutrition
                                                    .serving_size
                                            }}
                                        </p>

                                        <div
                                            class="border-t-4 border-ink my-1 pt-1 flex justify-between font-extrabold text-sm">
                                            <span>Calories</span>

                                            <span>
                                                {{
                                                    modalStore.product
                                                        .nutrition
                                                        .calories
                                                }}
                                            </span>
                                        </div>

                                        <div class="border-t border-ink pt-1 text-[11px] space-y-1">
                                            <div class="flex justify-between font-semibold">
                                                <span>Total Fat</span>

                                                <span>
                                                    {{
                                                        modalStore.product
                                                            .nutrition
                                                            .fat_g
                                                    }}g
                                                </span>
                                            </div>

                                            <div class="flex justify-between font-semibold">
                                                <span>Carbohydrates</span>

                                                <span>
                                                    {{
                                                        modalStore.product
                                                            .nutrition
                                                            .carbs_g
                                                    }}g
                                                </span>
                                            </div>

                                            <div class="flex justify-between font-semibold">
                                                <span>Protein</span>

                                                <span>
                                                    {{
                                                        modalStore.product
                                                            .nutrition
                                                            .protein_g
                                                    }}g
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- REVIEWS -->
                            <div class="bg-white rounded-2xl p-4 sm:p-6 shadow-sm border border-brand-caramel/20">
                                <ProductReviews :product-id="modalStore.product.id
                                    " />
                            </div>
                        </div>
                    </div>
                </Transition>
            </div>
        </Transition>
    </Teleport>
</template>

<script setup>
import {
    ref,
    computed,
    watch,
    nextTick,
} from "vue";

import { useRouter } from "vue-router";
import axios from "axios";

import { useProductModalStore } from "@/stores/productModal";
import { useCartStore } from "@/stores/cart";
import { useWishlistStore } from "@/stores/wishlist";
import { useToast } from "@/composables/useToast";

import ProductReviews from "@/components/storefront/ProductReviews.vue";
import { Star, ShoppingBag, Cake, X, Clock, AlertTriangle, Check, Heart } from 'lucide-vue-next';

const modalStore = useProductModalStore();
const cartStore = useCartStore();
const wishlistStore = useWishlistStore();
const toast = useToast();
const router = useRouter();

/*
|--------------------------------------------------------------------------
| STATE
|--------------------------------------------------------------------------
*/

const activeIndex = ref(0);
const activeTab = ref("description");
const quantity = ref(1);
const adding = ref(false);
const productLoading = ref(false);

const touchStartX = ref(0);
const touchCurrentX = ref(0);

const selectedVariationIdx = ref(null);
const selectedFlavorIdx = ref(null);
const selectedFlavorIndexes = ref([]);

const failedImageUrls = ref(new Set());

/*
|--------------------------------------------------------------------------
| GUARANTEED LOCAL FALLBACK
|--------------------------------------------------------------------------
*/

const fallbackImageUrl =
    "data:image/svg+xml;charset=UTF-8," +
    encodeURIComponent(`
        <svg xmlns="http://www.w3.org/2000/svg" width="800" height="800" viewBox="0 0 800 800">
            <rect width="800" height="800" fill="#f7efe5"/>
            <circle cx="400" cy="340" r="150" fill="#ead7c0"/>
            <text x="400" y="350"
                  text-anchor="middle"
                  font-size="110">
                
            </text>
            <text x="400" y="530"
                  text-anchor="middle"
                  font-family="Arial, sans-serif"
                  font-size="28"
                  fill="#6b4428">
                ABCDips &amp; Treats
            </text>
        </svg>
    `);

/*
|--------------------------------------------------------------------------
| GENERIC HELPERS
|--------------------------------------------------------------------------
*/

function parseJson(value) {
    if (typeof value !== "string") {
        return value;
    }

    const trimmed = value.trim();

    if (!trimmed) {
        return null;
    }

    try {
        return JSON.parse(trimmed);
    } catch {
        return value;
    }
}

function getArray(value) {
    value = parseJson(value);

    if (Array.isArray(value)) {
        return value;
    }

    /*
     * Handles:
     *
     * {
     *   "0": {...},
     *   "1": {...}
     * }
     */
    if (
        value &&
        typeof value === "object" &&
        !Array.isArray(value)
    ) {
        if (Array.isArray(value.data)) {
            return value.data;
        }

        if (Array.isArray(value.items)) {
            return value.items;
        }

        const values = Object.values(value);

        if (values.length && values.every((item) => item != null)) {
            return values;
        }
    }

    return [];
}

function getOptionKey(option, index) {
    if (!option) {
        return index;
    }

    if (typeof option !== "object") {
        return String(option);
    }

    return (
        option.id ??
        option.value ??
        option.slug ??
        option.name ??
        option.label ??
        option.title ??
        index
    );
}

/*
|--------------------------------------------------------------------------
| OPTION LABELS
|--------------------------------------------------------------------------
*/

function getFlavorName(flavor) {
    if (flavor === null || flavor === undefined) {
        return "";
    }

    if (typeof flavor === "string") {
        return flavor;
    }

    if (typeof flavor !== "object") {
        return String(flavor);
    }

    return (
        flavor.name ??
        flavor.label ??
        flavor.value ??
        flavor.title ??
        flavor.flavor ??
        flavor.flavor_name ??
        ""
    );
}

function getVariationLabel(variation) {
    if (variation === null || variation === undefined) {
        return "";
    }

    if (typeof variation === "string") {
        return variation;
    }

    if (typeof variation !== "object") {
        return String(variation);
    }

    return (
        variation.label ??
        variation.name ??
        variation.value ??
        variation.title ??
        variation.variation ??
        variation.variation_name ??
        ""
    );
}

function getAllergenName(allergen) {
    if (!allergen) {
        return "";
    }

    if (typeof allergen === "string") {
        return allergen;
    }

    return (
        allergen.name ??
        allergen.label ??
        allergen.value ??
        allergen.title ??
        ""
    );
}

function getAllergenType(allergen) {
    if (!allergen || typeof allergen !== "object") {
        return "";
    }

    return (
        allergen.type ??
        allergen.category ??
        allergen.kind ??
        ""
    );
}

/*
|--------------------------------------------------------------------------
| PRICE MODIFIER
|--------------------------------------------------------------------------
*/

function getPriceModifier(option) {
    if (!option || typeof option !== "object") {
        return 0;
    }

    return Number(
        option.price_modifier ??
        option.priceModifier ??
        option.modifier ??
        option.additional_price ??
        option.additionalPrice ??
        option.extra_price ??
        option.extraPrice ??
        0
    );
}

/*
|--------------------------------------------------------------------------
| PRODUCT OPTION NORMALIZATION
|--------------------------------------------------------------------------
*/

const normalizedVariations = computed(() => {
    const product = modalStore.product;

    if (!product) {
        return [];
    }

    const possibleValues = [
        product.variations,
        product.variation_options,
        product.variationOptions,
        product.options?.variations,
        product.options?.variation_options,
        product.product_variations,
    ];

    for (const value of possibleValues) {
        const array = getArray(value);

        if (array.length) {
            return array;
        }
    }

    return [];
});

const normalizedFlavors = computed(() => {
    const product = modalStore.product;

    if (!product) {
        return [];
    }

    const possibleValues = [
        product.flavors,
        product.flavor_options,
        product.flavorOptions,
        product.options?.flavors,
        product.options?.flavor_options,
        product.product_flavors,
    ];

    for (const value of possibleValues) {
        const array = getArray(value);

        if (array.length) {
            return array;
        }
    }

    return [];
});

const normalizedAllergens = computed(() => {
    const product = modalStore.product;

    if (!product) {
        return [];
    }

    return getArray(
        product.allergens ??
        product.allergen_information ??
        product.allergenInformation
    );
});

/*
|--------------------------------------------------------------------------
| IMAGE NORMALIZATION
|--------------------------------------------------------------------------
*/

function normalizeImageUrl(image) {
    image = parseJson(image);

    if (!image) {
        return null;
    }

    if (typeof image === "string") {
        let value = image.trim();

        if (!value) {
            return null;
        }

        /*
         * Absolute URLs
         */
        if (
            value.startsWith("http://") ||
            value.startsWith("https://") ||
            value.startsWith("data:image/")
        ) {
            return value;
        }

        /*
         * Protocol-relative URL
         */
        if (value.startsWith("//")) {
            return `${window.location.protocol}${value}`;
        }

        /*
         * Laravel storage paths
         */
        if (value.startsWith("/storage/")) {
            return value;
        }

        if (value.startsWith("storage/")) {
            return `/${value}`;
        }

        /*
         * Public images
         */
        if (value.startsWith("/images/")) {
            return value;
        }

        if (value.startsWith("images/")) {
            return `/${value}`;
        }

        /*
         * Public root paths
         */
        if (value.startsWith("/")) {
            return value;
        }

        /*
         * Most database image paths are stored like:
         *
         * products/foo.jpg
         *
         * Convert to:
         *
         * /storage/products/foo.jpg
         */
        return `/storage/${value.replace(/^\/+/, "")}`;
    }

    if (typeof image === "object") {
        return normalizeImageUrl(
            image.url ??
            image.src ??
            image.path ??
            image.file_path ??
            image.filePath ??
            image.image_url ??
            image.imageUrl ??
            image.original_url ??
            image.originalUrl ??
            image.preview_url ??
            image.previewUrl ??
            image.media_url ??
            image.mediaUrl
        );
    }

    return null;
}

/*
|--------------------------------------------------------------------------
| COLLECT ALL PRODUCT IMAGES
|--------------------------------------------------------------------------
*/

const allImages = computed(() => {
    const product = modalStore.product;

    if (!product) {
        return [];
    }

    const images = [];

    function addImage(value) {
        if (!value) {
            return;
        }

        const url = normalizeImageUrl(value);

        if (url && !images.includes(url)) {
            images.push(url);
        }
    }

    function addCollection(value) {
        value = parseJson(value);

        if (!value) {
            return;
        }

        if (Array.isArray(value)) {
            value.forEach(addImage);
            return;
        }

        if (
            typeof value === "object" &&
            Array.isArray(value.data)
        ) {
            value.data.forEach(addImage);
            return;
        }

        addImage(value);
    }

    /*
     * Primary image fields
     */
    [
        product.primary_image_url,
        product.primaryImageUrl,
        product.image_url,
        product.imageUrl,
        product.featured_image,
        product.featuredImage,
        product.image,
        product.primary_image,
        product.primaryImage,
        product.thumbnail,
        product.thumbnail_url,
        product.thumbnailUrl,
    ].forEach(addImage);

    /*
     * Gallery fields
     */
    [
        product.images,
        product.gallery_images,
        product.gallery_image_urls,
        product.galleryImages,
        product.secondary_images,
        product.secondary_images_urls,
        product.secondaryImages,
        product.product_images,
        product.productImages,
        product.media,
        product.product_media,
    ].forEach(addCollection);

    return images;
});

/*
|--------------------------------------------------------------------------
| REMOVE BROKEN IMAGES
|--------------------------------------------------------------------------
*/

const displayImages = computed(() => {
    const validImages = allImages.value.filter(
        (url) => !failedImageUrls.value.has(url)
    );

    return validImages.length
        ? validImages
        : [fallbackImageUrl];
});

function handleImageError(url) {
    if (!url || url === fallbackImageUrl) {
        return;
    }

    const next = new Set(failedImageUrls.value);
    next.add(url);

    failedImageUrls.value = next;

    if (
        activeIndex.value >=
        displayImages.value.length - 1
    ) {
        activeIndex.value = Math.max(
            0,
            displayImages.value.length - 2
        );
    }
}

/*
|--------------------------------------------------------------------------
| PRODUCT REFRESH
|--------------------------------------------------------------------------
*/

async function refreshProductForEditing() {
    const editingItem = modalStore.editingCartItem;

    if (!editingItem) {
        return;
    }

    const currentProduct = modalStore.product;

    const slug =
        currentProduct?.slug ??
        editingItem?.slug ??
        editingItem?.product?.slug ??
        editingItem?.product_slug ??
        editingItem?.productSlug ??
        editingItem?.product_id;

    if (!slug) {
        console.warn(
            "Unable to refresh product: product slug not found.",
            editingItem
        );

        return;
    }

    productLoading.value = true;

    try {
        const response = await axios.get(
            `/api/products/${encodeURIComponent(slug)}`
        );

        const fetchedProduct =
            response?.data?.data ??
            response?.data?.product ??
            response?.data;

        if (
            fetchedProduct &&
            typeof fetchedProduct === "object"
        ) {
            /*
             * IMPORTANT:
             *
             * Keep existing cart/product data but let the complete
             * API response override it.
             */
            modalStore.product = {
                ...currentProduct,
                ...fetchedProduct,
            };

            /*
             * Clear failed images because this is a fresh product
             * response.
             */
            failedImageUrls.value = new Set();

            activeIndex.value = 0;
        }
    } catch (error) {
        console.error(
            "Failed to refresh product while editing cart item:",
            error
        );
    } finally {
        productLoading.value = false;
    }
}

/*
|--------------------------------------------------------------------------
| CART OPTION NORMALIZATION
|--------------------------------------------------------------------------
*/

function normalizeOptionObject(value) {
    value = parseJson(value);

    if (!value) {
        return {};
    }

    if (Array.isArray(value)) {
        const result = {};

        for (const item of value) {
            if (!item || typeof item !== "object") {
                continue;
            }

            const key =
                item.key ??
                item.name ??
                item.type ??
                item.option ??
                item.code;

            const val =
                item.value ??
                item.selected ??
                item.label ??
                item.name;

            if (key) {
                result[key] = val;
            }
        }

        return result;
    }

    if (typeof value === "object") {
        return value;
    }

    return {};
}

function getEditingOptionSources() {
    const item = modalStore.editingCartItem;

    if (!item) {
        return [];
    }

    return [
        item,
        item.options,
        item.cart_options,
        item.cartOptions,
        item.product_options,
        item.productOptions,
        item.product_options_data,
        item.productOptionsData,
        item.configuration,
        item.config,
        item.variant,
        item.variant_data,
        item.variantData,
    ]
        .filter(Boolean)
        .map(normalizeOptionObject);
}

function getValueFromSources(keys) {
    const sources = getEditingOptionSources();

    for (const source of sources) {
        for (const key of keys) {
            if (
                source[key] !== undefined &&
                source[key] !== null &&
                source[key] !== ""
            ) {
                return source[key];
            }
        }
    }

    return null;
}

function getEditingVariation() {
    const direct = getValueFromSources([
        "variation_id",
        "variationId",
        "selected_variation_id",
        "selectedVariationId",
    ]);

    if (direct !== null) {
        return direct;
    }

    return getValueFromSources([
        "variation",
        "variation_name",
        "variationName",
        "variation_label",
        "variationLabel",
        "selected_variation",
        "selectedVariation",
        "selected_option",
        "selectedOption",
    ]);
}

function getEditingFlavor() {
    const direct = getValueFromSources([
        "flavor_id",
        "flavorId",
        "selected_flavor_id",
        "selectedFlavorId",
    ]);

    if (direct !== null) {
        return direct;
    }

    return getValueFromSources([
        "flavor",
        "flavor_name",
        "flavorName",
        "flavor_label",
        "flavorLabel",
        "selected_flavor",
        "selectedFlavor",
    ]);
}

/*
|--------------------------------------------------------------------------
| COMPARE OPTION VALUES
|--------------------------------------------------------------------------
*/

function normalizeCompareValue(value) {
    if (value === null || value === undefined) {
        return "";
    }

    if (typeof value === "object") {
        return [
            value.id,
            value.value,
            value.slug,
            value.name,
            value.label,
            value.title,
        ]
            .filter(
                (item) =>
                    item !== null &&
                    item !== undefined &&
                    item !== ""
            )
            .map((item) =>
                String(item).trim().toLowerCase()
            );
    }

    return [
        String(value)
            .trim()
            .toLowerCase(),
    ];
}

function optionMatches(option, target) {
    if (
        target === null ||
        target === undefined ||
        target === ""
    ) {
        return false;
    }

    const targetValues =
        normalizeCompareValue(target);

    const optionValues =
        normalizeCompareValue(option);

    return targetValues.some((targetValue) =>
        optionValues.includes(targetValue)
    );
}

/*
|--------------------------------------------------------------------------
| FIND VARIATION INDEX
|--------------------------------------------------------------------------
*/

function findVariationIndex(value) {
    if (
        value === null ||
        value === undefined ||
        value === ""
    ) {
        return null;
    }

    const index =
        normalizedVariations.value.findIndex(
            (variation) =>
                optionMatches(variation, value)
        );

    return index >= 0 ? index : null;
}

/*
|--------------------------------------------------------------------------
| FIND FLAVOR INDEX
|--------------------------------------------------------------------------
*/

function findFlavorIndex(value) {
    if (
        value === null ||
        value === undefined ||
        value === ""
    ) {
        return null;
    }

    const index =
        normalizedFlavors.value.findIndex(
            (flavor) =>
                optionMatches(flavor, value)
        );

    return index >= 0 ? index : null;
}

/*
|--------------------------------------------------------------------------
| RESTORE EDITING STATE
|--------------------------------------------------------------------------
*/

function restoreEditingState() {
    const item = modalStore.editingCartItem;

    if (!item) {
        quantity.value = 1;
        selectedVariationIdx.value = normalizedVariations.value.length > 0 ? 0 : null;
        selectedFlavorIdx.value = null;
        selectedFlavorIndexes.value = [];

        return;
    }

    quantity.value = Math.max(
        1,
        Number(
            item.quantity ??
            item.qty ??
            item.pivot?.quantity ??
            1
        )
    );

    const existingVariation =
        getEditingVariation();

    const existingFlavor =
        getEditingFlavor();

    const existingFlavors =
        getValueFromSources(["flavors", "selected_flavors", "selectedFlavors"]);

    selectedVariationIdx.value =
        findVariationIndex(
            existingVariation
        );

    if (Array.isArray(existingFlavors) && existingFlavors.length > 0) {
        const idxs = [];
        for (const flvName of existingFlavors) {
            const idx = findFlavorIndex(flvName);
            if (idx !== null && !idxs.includes(idx)) {
                idxs.push(idx);
            }
        }
        selectedFlavorIndexes.value = idxs;
        selectedFlavorIdx.value = idxs[0] ?? null;
    } else if (typeof existingFlavor === "string" && existingFlavor.includes(",")) {
        const parts = existingFlavor.split(",").map(s => s.replace(/\s*\([+-]?₱[\d.]+\)/g, "").trim());
        const idxs = [];
        for (const part of parts) {
            const idx = findFlavorIndex(part);
            if (idx !== null && !idxs.includes(idx)) {
                idxs.push(idx);
            }
        }
        selectedFlavorIndexes.value = idxs;
        selectedFlavorIdx.value = idxs[0] ?? null;
    } else {
        selectedFlavorIdx.value =
            findFlavorIndex(
                existingFlavor
            );
        if (selectedFlavorIdx.value !== null) {
            selectedFlavorIndexes.value = [selectedFlavorIdx.value];
        } else {
            selectedFlavorIndexes.value = [];
        }
    }
}

/*
|--------------------------------------------------------------------------
| WATCH MODAL
|--------------------------------------------------------------------------
*/

let isRestoring = false;

watch(
    [
        () => modalStore.product?.id,
        () => modalStore.editingCartItem?.id,
        () => modalStore.isOpen,
    ],
    async ([productId, editingItemId, isOpen]) => {
        if (!isOpen) return;
        if (isRestoring) return;

        isRestoring = true;

        try {
            activeIndex.value = 0;
            activeTab.value = "description";
            failedImageUrls.value = new Set();

            if (!modalStore.product) {
                quantity.value = 1;
                selectedVariationIdx.value = null;
                selectedFlavorIdx.value = null;
                selectedFlavorIndexes.value = [];
                return;
            }

            if (modalStore.editingCartItem) {
                restoreEditingState();
                await refreshProductForEditing();
                await nextTick();
                restoreEditingState();
            } else {
                quantity.value = 1;
                selectedVariationIdx.value = null;
                selectedFlavorIdx.value = null;
                selectedFlavorIndexes.value = [];
            }
        } finally {
            isRestoring = false;
        }
    },
    {
        immediate: true,
        deep: false,
    }
);

/*
|--------------------------------------------------------------------------
| VARIATION LABEL
|--------------------------------------------------------------------------
*/

const variationLabel = computed(() => {
    const type =
        modalStore.product?.variation_type;

    if (!type || type === "none") {
        return "Select Option";
    }

    const lower = String(type)
        .toLowerCase()
        .trim();

    const known = {
        weight: "Select Weight / Grams",
        pieces: "Select Quantity (Pieces)",
        size: "Select Size",
        packaging: "Select Packaging",
        bundle: "Select Bundle",
        assorted: "Select Box / Package Type",
        flavor: "Select Option / Quantity",
    };

    return (
        known[lower] ??
        `Select ${String(type)
            .split(" ")
            .map(
                (word) =>
                    word.charAt(0).toUpperCase() +
                    word.slice(1)
            )
            .join(" ")}`
    );
});

/*
|--------------------------------------------------------------------------
| SELECTED OPTIONS
|--------------------------------------------------------------------------
*/

const selectedVariation = computed(() => {
    if (
        selectedVariationIdx.value === null
    ) {
        return null;
    }

    return (
        normalizedVariations.value[
        selectedVariationIdx.value
        ] ?? null
    );
});

/*
|--------------------------------------------------------------------------
| ASSORTED & MULTI-FLAVOR DETECTION
|--------------------------------------------------------------------------
*/

const isAssorted = computed(() => {
    const p = modalStore.product;
    if (!p) return false;
    if (p.is_assorted) return true;
    if (p.variation_type === 'assorted') return true;
    if (p.name && p.name.toLowerCase().includes('assorted')) return true;
    const currentVar = selectedVariation.value;
    if (currentVar) {
        const varLabel = getVariationLabel(currentVar).toLowerCase();
        if (varLabel.includes('assorted') || varLabel.includes('mix') || varLabel.includes('bundle')) {
            return true;
        }
    }
    return false;
});

const maxAllowedFlavors = computed(() => {
    const p = modalStore.product;
    if (p?.max_flavors && Number(p.max_flavors) > 0) {
        return Number(p.max_flavors);
    }
    const currentVar = selectedVariation.value;
    if (currentVar) {
        const varLabel = getVariationLabel(currentVar);
        const match = varLabel.match(/(\d+)\s*(?:pcs|pc|pieces|pack|box)/i);
        if (match && parseInt(match[1], 10) > 0) {
            return parseInt(match[1], 10);
        }
    }
    return null;
});

function isFlavorSelected(idx) {
    if (isAssorted.value) {
        return selectedFlavorIndexes.value.includes(idx);
    }
    return selectedFlavorIdx.value === idx;
}

function toggleFlavor(idx) {
    if (isAssorted.value) {
        const pos = selectedFlavorIndexes.value.indexOf(idx);
        if (pos >= 0) {
            selectedFlavorIndexes.value.splice(pos, 1);
        } else {
            if (maxAllowedFlavors.value && selectedFlavorIndexes.value.length >= maxAllowedFlavors.value) {
                toast.warning(`You can select a maximum of ${maxAllowedFlavors.value} flavor(s).`, "Flavor Limit Reached");
                return;
            }
            selectedFlavorIndexes.value.push(idx);
        }
    } else {
        selectedFlavorIdx.value = selectedFlavorIdx.value === idx ? null : idx;
    }
}

const selectedFlavors = computed(() => {
    if (isAssorted.value) {
        return selectedFlavorIndexes.value
            .map((idx) => normalizedFlavors.value[idx])
            .filter(Boolean);
    }
    if (selectedFlavorIdx.value !== null) {
        const flv = normalizedFlavors.value[selectedFlavorIdx.value];
        return flv ? [flv] : [];
    }
    return [];
});

const totalFlavorPriceModifier = computed(() => {
    return selectedFlavors.value.reduce(
        (sum, flv) => sum + getPriceModifier(flv),
        0
    );
});

const selectedFlavor = computed(() => {
    if (selectedFlavors.value.length > 0) {
        return selectedFlavors.value[0];
    }
    return null;
});

/*
|--------------------------------------------------------------------------
| PRICE
|--------------------------------------------------------------------------
*/

const effectivePrice = computed(() => {
    const product = modalStore.product;

    if (!product) {
        return 0;
    }

    const base = Number(
        product.sale_price ??
        product.price ??
        0
    );

    return (
        base +
        getPriceModifier(
            selectedVariation.value
        ) +
        totalFlavorPriceModifier.value
    );
});

const hasPrice = computed(() => {
    return (
        Number(
            modalStore.product?.price ?? 0
        ) > 0
    );
});

/*
|--------------------------------------------------------------------------
| EDITING
|--------------------------------------------------------------------------
*/

const isEditing = computed(() => {
    return !!modalStore.editingCartItem;
});

/*
|--------------------------------------------------------------------------
| ACTION DISABLED
|--------------------------------------------------------------------------
*/

const actionDisabled = computed(() => {
    if (
        adding.value ||
        productLoading.value
    ) {
        return true;
    }

    /*
     * Existing cart item:
     *
     * ALWAYS allow editing.
     */
    if (isEditing.value) {
        return false;
    }

    /*
     * New item:
     *
     * Respect stock.
     */
    return !modalStore.product?.is_in_stock;
});

/*
|--------------------------------------------------------------------------
| MAX QUANTITY
|--------------------------------------------------------------------------
*/

const maxQuantityReached = computed(() => {
    if (isEditing.value) {
        return false;
    }

    const stock = Number(
        modalStore.product?.stock_qty ?? 0
    );

    return (
        stock > 0 &&
        quantity.value >= stock
    );
});

/*
|--------------------------------------------------------------------------
| QUANTITY
|--------------------------------------------------------------------------
*/

function decreaseQuantity() {
    if (quantity.value > 1) {
        quantity.value--;
    }
}

function increaseQuantity() {
    if (!maxQuantityReached.value) {
        quantity.value++;
    }
}

function onQuantityInput(event) {
    const raw = parseInt(event.target.value, 10);
    if (!isNaN(raw)) {
        const stock = Number(modalStore.product?.stock_qty ?? 0);
        const max = stock > 0 ? stock : 999;
        // Let the user keep typing, just cap silently
        quantity.value = Math.max(1, Math.min(raw, max));
    }
}

function onQuantityBlur(event) {
    const raw = parseInt(event.target.value, 10);
    const stock = Number(modalStore.product?.stock_qty ?? 0);
    const max = stock > 0 ? stock : 999;
    if (isNaN(raw) || raw < 1) {
        quantity.value = 1;
    } else {
        quantity.value = Math.min(raw, max);
    }
}

/*
|--------------------------------------------------------------------------
| IMAGE CAROUSEL
|--------------------------------------------------------------------------
*/

function prevImage() {
    if (activeIndex.value > 0) {
        activeIndex.value--;
    }
}

function nextImage() {
    if (
        activeIndex.value <
        displayImages.value.length - 1
    ) {
        activeIndex.value++;
    }
}

/*
|--------------------------------------------------------------------------
| TOUCH
|--------------------------------------------------------------------------
*/

function onTouchStart(event) {
    touchStartX.value =
        event.touches[0].clientX;

    touchCurrentX.value =
        event.touches[0].clientX;
}

function onTouchMove(event) {
    touchCurrentX.value =
        event.touches[0].clientX;
}

function onTouchEnd() {
    if (
        !touchStartX.value ||
        !touchCurrentX.value
    ) {
        return;
    }

    const diff =
        touchStartX.value -
        touchCurrentX.value;

    if (Math.abs(diff) > 30) {
        if (diff > 0) {
            nextImage();
        } else {
            prevImage();
        }
    }

    touchStartX.value = 0;
    touchCurrentX.value = 0;
}

/*
|--------------------------------------------------------------------------
| BUILD CART OPTIONS
|--------------------------------------------------------------------------
*/

function buildCartOptions() {
    const options = {};

    /*
     * FLAVOR(S)
     */
    if (isAssorted.value) {
        if (selectedFlavors.value.length > 0) {
            options.is_assorted = true;
            options.flavors = selectedFlavors.value.map((f) => getFlavorName(f));
            options.flavor = selectedFlavors.value
                .map((f) => {
                    const mod = getPriceModifier(f);
                    return mod !== 0
                        ? `${getFlavorName(f)} (${mod > 0 ? "+" : ""}₱${mod.toFixed(2)})`
                        : getFlavorName(f);
                })
                .join(", ");
            options.flavor_price_modifier = totalFlavorPriceModifier.value;
        }
    } else if (selectedFlavor.value) {
        const flavor =
            selectedFlavor.value;

        options.flavor =
            getFlavorName(flavor);

        options.flavor_id =
            typeof flavor === "object"
                ? flavor.id ??
                flavor.flavor_id ??
                null
                : null;

        options.flavor_price_modifier =
            getPriceModifier(flavor);
    } else if (
        modalStore.product?.flavor
    ) {
        options.flavor =
            modalStore.product.flavor;
    }

    /*
     * VARIATION
     */
    if (selectedVariation.value) {
        const variation =
            selectedVariation.value;

        options.variation =
            getVariationLabel(
                variation
            );

        options.variation_id =
            typeof variation === "object"
                ? variation.id ??
                variation.variation_id ??
                null
                : null;

        options.price_modifier =
            getPriceModifier(
                variation
            );
    }

    /*
     * PRICE
     */
    options.unit_price =
        Number(
            effectivePrice.value.toFixed(2)
        );

    return options;
}

/*
|--------------------------------------------------------------------------
| VALIDATE
|--------------------------------------------------------------------------
*/

function validateSelection() {
    const product =
        modalStore.product;

    if (!product) {
        return false;
    }

    const requiresVariation =
        normalizedVariations.value
            .length > 0 &&
        product.variation_type &&
        product.variation_type !==
        "none";

    if (
        requiresVariation &&
        selectedVariationIdx.value ===
        null
    ) {
        if (normalizedVariations.value.length > 0) {
            selectedVariationIdx.value = 0;
            return true;
        }

        toast.warning(
            `Please select a ${variationLabel.value
                .toLowerCase()
                .replace(/^select /, "")} option before proceeding.`,
            "Variation Required"
        );

        return false;
    }

    if (isAssorted.value && normalizedFlavors.value.length > 0 && selectedFlavorIndexes.value.length === 0) {
        toast.warning(
            "Please select at least one flavor for your assorted selection.",
            "Flavor Selection Required"
        );
        return false;
    }

    return true;
}

/*
|--------------------------------------------------------------------------
| ADD / UPDATE
|--------------------------------------------------------------------------
*/

async function handleAddToCart() {
    if (
        !modalStore.product ||
        adding.value
    ) {
        return;
    }

    if (!validateSelection()) {
        return;
    }

    if (
        !isEditing.value &&
        !modalStore.product.is_in_stock
    ) {
        toast.warning(
            "This product is currently out of stock.",
            "Out of Stock"
        );

        return;
    }

    adding.value = true;

    try {
        const options =
            buildCartOptions();

        let response;

        if (isEditing.value) {
            response =
                await cartStore.updateItem(
                    modalStore
                        .editingCartItem.id,
                    quantity.value,
                    options
                );
        } else {
            response =
                await cartStore.addItem(
                    modalStore.product.id,
                    quantity.value,
                    options
                );
        }

        if (response?.success) {
            toast.success(
                isEditing.value
                    ? `Updated ${modalStore.product.name} in your basket!`
                    : `Added ${quantity.value}x ${modalStore.product.name} to your basket!`,
                isEditing.value
                    ? "Basket Item Updated"
                    : "Freshly Baked"
            );

            modalStore.closeModal();
        }
    } catch (error) {
        console.error(
            "Cart operation failed:",
            error
        );

        toast.error(
            error?.response?.data?.message ??
            "Unable to update your basket. Please try again.",
            "Basket Error"
        );
    } finally {
        adding.value = false;
    }
}

/*
|--------------------------------------------------------------------------
| BUY NOW / SAVE CHANGES
|--------------------------------------------------------------------------
*/

async function handleBuyNow() {
    if (
        !modalStore.product ||
        adding.value
    ) {
        return;
    }

    if (!validateSelection()) {
        return;
    }

    if (
        !isEditing.value &&
        !modalStore.product.is_in_stock
    ) {
        toast.warning(
            "This product is currently out of stock.",
            "Out of Stock"
        );

        return;
    }

    adding.value = true;

    try {
        const options =
            buildCartOptions();

        let response;

        if (isEditing.value) {
            response =
                await cartStore.updateItem(
                    modalStore
                        .editingCartItem.id,
                    quantity.value,
                    options
                );
        } else {
            response =
                await cartStore.addItem(
                    modalStore.product.id,
                    quantity.value,
                    options
                );
        }

        if (response?.success) {
            if (isEditing.value) {
                toast.success(
                    "Basket updated! Proceeding to checkout.",
                    "Checkout Ready"
                );
            } else {
                toast.success(
                    "Basket saved! Proceeding to checkout.",
                    "Checkout Ready"
                );
            }

            modalStore.closeModal();

            router.push("/checkout");
        }
    } catch (error) {
        console.error(
            "Checkout cart operation failed:",
            error
        );

        toast.error(
            error?.response?.data?.message ??
            "Unable to update your basket. Please try again.",
            "Basket Error"
        );
    } finally {
        adding.value = false;
    }
}
</script>

<style scoped>
.no-scrollbar::-webkit-scrollbar {
    display: none;
}

.no-scrollbar {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
</style>
