<template>
  <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4 pb-12">
    <div class="relative bg-gradient-to-br from-[#1C1410] via-[#160E0A] to-[#100906] text-[#FBF3E7] rounded-3xl overflow-hidden border border-[#C08E5D]/30 shadow-2xl">
      
      <!-- Ambient Studio Lighting Aura -->
      <div class="absolute -top-32 -left-32 w-96 h-96 rounded-full blur-3xl pointer-events-none transition-all duration-700"
           :style="{ background: currentLighting.auraLeft }" />
      <div class="absolute -bottom-32 -right-32 w-96 h-96 rounded-full blur-3xl pointer-events-none transition-all duration-700"
           :style="{ background: currentLighting.auraRight }" />
      <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_center,rgba(217,168,118,0.06)_0%,transparent_70%)] pointer-events-none" />

      <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-center p-6 sm:p-10 lg:p-14 relative z-10">
        
        <!-- Left: Authentic ABCDips Business Presentation -->
        <div class="lg:col-span-5 space-y-6 text-left">
          
          <!-- Badge -->
          <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-[#D9A876]/15 border border-[#C08E5D]/30 text-xs font-bold text-[#E2C08A] tracking-wider uppercase backdrop-blur-md">
            <Sparkles class="w-3.5 h-3.5 text-[#E2C08A] animate-pulse" />
            <span>ABCDips &amp; Treats • Best Seller</span>
          </div>

          <!-- Title & Authentic Bakery Description -->
          <div class="space-y-3">
            <h2 class="text-3xl sm:text-4xl lg:text-5xl font-black text-[#FBF3E7] tracking-tight leading-[1.15]">
              Classic Banana <br />
              <span class="font-['Caveat'] text-[#E2C08A] font-normal text-4xl sm:text-5xl block mt-0.5">
                Bread Loaf
              </span>
            </h2>
            <p class="text-sm sm:text-base text-[#FBF3E7]/85 leading-relaxed max-w-lg">
              Our signature best-selling banana bread, baked fresh daily in Bacoor, Cavite. Made with sweet ripe bananas, 100% pure creamery butter, and topped with crunchy toasted almonds and chocolate chips.
            </p>
          </div>

          <!-- Authentic Trust Highlights -->
          <div class="space-y-3 pt-2 border-t border-[#C08E5D]/20">
            <div class="flex items-center gap-3 text-xs sm:text-sm text-[#FBF3E7]/90 font-medium">
              <CheckCircle2 class="w-4 h-4 text-emerald-400 shrink-0" />
              <span>100% Real Creamery Butter &amp; Sweet Ripe Bananas</span>
            </div>
            <div class="flex items-center gap-3 text-xs sm:text-sm text-[#FBF3E7]/90 font-medium">
              <CheckCircle2 class="w-4 h-4 text-emerald-400 shrink-0" />
              <span>Baked Fresh Daily in Small Hearth Batches</span>
            </div>
            <div class="flex items-center gap-3 text-xs sm:text-sm text-[#FBF3E7]/90 font-medium">
              <CheckCircle2 class="w-4 h-4 text-emerald-400 shrink-0" />
              <span>Same-Day Delivery &amp; Pickup Available in Cavite</span>
            </div>
          </div>

          <!-- Action Row (Real Product URL & Full Menu) -->
          <div class="flex flex-wrap items-center gap-4 pt-4">
            <RouterLink to="/products/classic-banana-bread-loaf"
                        class="bg-gradient-to-r from-[#D9A876] to-[#C08E5D] text-[#1C1410] px-7 py-3.5 rounded-2xl font-extrabold text-sm hover:opacity-95 shadow-lg shadow-[#C08E5D]/20 hover:scale-[1.02] active:scale-[0.98] transition-all flex items-center gap-2">
              <ShoppingBag class="w-4 h-4 text-[#1C1410]" />
              <span>Order Banana Bread • ₱280.00</span>
            </RouterLink>
            <RouterLink to="/shop"
                        class="px-5 py-3.5 rounded-2xl font-bold text-xs sm:text-sm text-[#E2C08A] hover:text-[#FBF3E7] hover:bg-white/5 border border-[#C08E5D]/30 transition-all flex items-center gap-1.5">
              <span>Browse Full Menu</span>
              <ArrowRight class="w-3.5 h-3.5" />
            </RouterLink>
          </div>

        </div>

        <!-- Right: Real-time 3D WebGL Interactive Viewport -->
        <div class="lg:col-span-7 flex flex-col items-center justify-center relative">
          
          <!-- Viewport Stage -->
          <div class="w-full aspect-[4/3] sm:aspect-[16/11] rounded-3xl bg-gradient-to-b from-[#22160E]/90 to-[#120B07] border border-[#C08E5D]/30 backdrop-blur-xl relative overflow-hidden shadow-2xl select-none group">
            
            <!-- Three.js Canvas Container -->
            <div ref="canvasContainerRef"
                 class="w-full h-full cursor-grab active:cursor-grabbing select-none"
                 @mousedown="handleMouseDown"
                 @mousemove="handleMouseMove"
                 @mouseup="handleMouseUp"
                 @mouseleave="handleMouseLeave"
                 @touchstart.passive="handleTouchStart"
                 @touchmove.passive="handleTouchMove"
                 @touchend="handleTouchEnd" />

            <!-- Drag Instruction Badge -->
            <div class="absolute bottom-4 left-4 z-20 pointer-events-none flex items-center gap-2 bg-[#1C1410]/90 backdrop-blur-md px-3.5 py-1.5 rounded-full border border-[#C08E5D]/30 text-[11px] font-mono text-[#E2C08A] shadow-lg">
              <Move3d class="w-3.5 h-3.5 animate-spin" style="animation-duration: 8s;" />
              <span>Drag to rotate 360°</span>
            </div>

            <!-- Controls Top Right -->
            <div class="absolute top-4 right-4 z-20 flex items-center gap-2">
              
              <!-- Auto-Rotate Toggle -->
              <button class="bg-[#1C1410]/90 backdrop-blur-md px-3.5 py-1.5 rounded-full border border-[#C08E5D]/30 text-[11px] font-bold text-[#FBF3E7] hover:bg-[#2A1C13] transition-all flex items-center gap-1.5 shadow-lg cursor-pointer"
                      @click="toggleAutoRotate">
                <RotateCcw class="w-3 h-3 text-[#E2C08A]" :class="isAutoRotating ? 'animate-spin' : ''" style="animation-duration: 8s;" />
                <span>{{ isAutoRotating ? 'Rotating' : 'Paused' }}</span>
              </button>

              <!-- Reset Angle -->
              <button class="bg-[#1C1410]/90 backdrop-blur-md p-1.5 rounded-full border border-[#C08E5D]/30 text-[#E2C08A] hover:text-[#FBF3E7] hover:bg-[#2A1C13] transition-all shadow-lg cursor-pointer"
                      @click="resetCamera"
                      v-tooltip="'Reset rotation angle'">
                <Compass class="w-3.5 h-3.5" />
              </button>
            </div>

            <!-- 3D Realistic Badge -->
            <div class="absolute top-4 left-4 z-20 bg-[#1C1410]/90 backdrop-blur-md px-3 py-1.5 rounded-full border border-[#C08E5D]/30 text-[10px] font-mono text-[#E2C08A] flex items-center gap-1.5 shadow-lg pointer-events-none">
              <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse" />
              <span>Artisanal 3D WebGL</span>
            </div>

            <!-- Loading Spinner -->
            <div v-if="!isReady"
                 class="absolute inset-0 z-30 flex flex-col items-center justify-center bg-[#140D09]/95 backdrop-blur-md">
              <div class="w-10 h-10 border-3 border-[#C08E5D]/30 border-t-[#D9A876] rounded-full animate-spin mb-3" />
              <p class="text-xs font-mono text-[#E2C08A] uppercase tracking-widest">Baking 3D Banana Bread...</p>
            </div>

          </div>

          <!-- Studio Lighting Mode Strip -->
          <div class="mt-4 w-full flex flex-wrap items-center justify-between gap-3 p-2.5 bg-[#1C1410]/80 rounded-2xl border border-[#C08E5D]/25 backdrop-blur-md shadow-lg">
            <span class="text-xs font-bold uppercase tracking-wider text-[#E2C08A]/80 font-mono px-2 flex items-center gap-1.5">
              <Sun class="w-3.5 h-3.5 text-[#E2C08A]" />
              <span>Bakery Studio Lighting:</span>
            </span>

            <div class="flex items-center gap-1.5 overflow-x-auto">
              <button v-for="mode in lightingModes" :key="mode.id"
                      @click="setLightingMode(mode.id)"
                      :class="[
                        'px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all whitespace-nowrap flex items-center gap-1.5 cursor-pointer',
                        activeLightingMode === mode.id
                          ? 'bg-gradient-to-r from-[#D9A876] to-[#C08E5D] text-[#1C1410] shadow-sm'
                          : 'text-[#FBF3E7]/80 hover:text-[#FBF3E7] hover:bg-white/5'
                      ]">
                <component :is="mode.icon" class="w-3 h-3" />
                <span>{{ mode.label }}</span>
              </button>
            </div>
          </div>

        </div>

      </div>

    </div>
  </section>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { Sparkles, ShoppingBag, ArrowRight, RotateCcw, Move3d, Compass, Sun, Flame, Moon, CheckCircle2 } from 'lucide-vue-next'
import * as THREE from 'three'

const canvasContainerRef = ref(null)
const isReady = ref(false)
const isAutoRotating = ref(true)
const activeLightingMode = ref('oven')

// Studio Lighting Modes
const lightingModes = [
  { id: 'oven', label: 'Warm Bakery Glow', icon: Flame },
  { id: 'daylight', label: 'Studio Sunlight', icon: Sun },
  { id: 'espresso', label: 'Hearth Evening', icon: Moon },
]

const currentLighting = computed(() => {
  if (activeLightingMode.value === 'daylight') {
    return {
      auraLeft: 'rgba(255, 235, 200, 0.22)',
      auraRight: 'rgba(200, 220, 255, 0.16)',
      keyColor: 0xfff6ec,
      ambientColor: 0x5a483a,
      keyIntensity: 2.2,
      ambientIntensity: 1.1,
      pointColor: 0xffeedd
    }
  }
  if (activeLightingMode.value === 'espresso') {
    return {
      auraLeft: 'rgba(192, 142, 93, 0.18)',
      auraRight: 'rgba(92, 58, 34, 0.32)',
      keyColor: 0xd9a876,
      ambientColor: 0x3d2415,
      keyIntensity: 1.8,
      ambientIntensity: 0.9,
      pointColor: 0xc08e5d
    }
  }
  // Default: Warm bakery oven glow
  return {
    auraLeft: 'rgba(217, 168, 118, 0.28)',
    auraRight: 'rgba(192, 142, 93, 0.24)',
    keyColor: 0xffb562,
    ambientColor: 0x542f17,
    keyIntensity: 2.1,
    ambientIntensity: 1.05,
    pointColor: 0xffc880
  }
})

// Three.js Runtime Instances
let scene, camera, renderer, showcaseGroup, pointLight, keyLight, ambientLight, rimLight, steamParticles
let rafId = null
let isDragging = false
let previousMouse = { x: 0, y: 0 }
let targetRotation = { x: 0.26, y: -0.42 }
let currentRotation = { x: 0.26, y: -0.42 }
let animTime = 0

function setLightingMode(modeId) {
  activeLightingMode.value = modeId
  if (keyLight && ambientLight && pointLight) {
    keyLight.color.setHex(currentLighting.value.keyColor)
    keyLight.intensity = currentLighting.value.keyIntensity
    ambientLight.color.setHex(currentLighting.value.ambientColor)
    ambientLight.intensity = currentLighting.value.ambientIntensity
    pointLight.color.setHex(currentLighting.value.pointColor)
  }
}

// Generate an ambient studio softbox environment map for high-end PBR reflections
function createStudioEnvironment() {
  const envCanvas = document.createElement('canvas')
  envCanvas.width = 512
  envCanvas.height = 256
  const ctx = envCanvas.getContext('2d')

  // Warm studio gradient
  const bgGrad = ctx.createLinearGradient(0, 0, 0, 256)
  bgGrad.addColorStop(0, '#3A2416')
  bgGrad.addColorStop(0.5, '#1A100A')
  bgGrad.addColorStop(1, '#0C0604')
  ctx.fillStyle = bgGrad
  ctx.fillRect(0, 0, 512, 256)

  // Softbox light rectangle 1 (overhead bakery warmth)
  ctx.fillStyle = 'rgba(255, 220, 170, 0.45)'
  ctx.filter = 'blur(16px)'
  ctx.beginPath()
  ctx.ellipse(256, 40, 140, 30, 0, 0, Math.PI * 2)
  ctx.fill()

  // Softbox light rectangle 2 (rim reflection)
  ctx.fillStyle = 'rgba(255, 240, 220, 0.35)'
  ctx.beginPath()
  ctx.ellipse(80, 100, 40, 60, 0, 0, Math.PI * 2)
  ctx.fill()
  ctx.filter = 'none'

  const envTexture = new THREE.CanvasTexture(envCanvas)
  envTexture.mapping = THREE.EquirectangularReflectionMapping
  return envTexture
}

function initThree() {
  if (!canvasContainerRef.value) return

  const container = canvasContainerRef.value
  const width = container.clientWidth || 600
  const height = container.clientHeight || 450

  // 1. Scene
  scene = new THREE.Scene()

  // 2. Camera (Cinematic 3/4 Eye-level Bakery Perspective)
  camera = new THREE.PerspectiveCamera(35, width / height, 0.1, 100)
  camera.position.set(0, 1.35, 3.45)
  camera.lookAt(0, 0.08, 0)

  // 3. Renderer with ACES Filmic Tone Mapping
  renderer = new THREE.WebGLRenderer({ antialias: true, alpha: true, powerPreference: 'high-performance' })
  renderer.setSize(width, height)
  renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2))
  renderer.toneMapping = THREE.ACESFilmicToneMapping
  renderer.toneMappingExposure = 1.25
  renderer.shadowMap.enabled = true
  renderer.shadowMap.type = THREE.PCFSoftShadowMap

  container.appendChild(renderer.domElement)

  // Studio Environment Reflection Map
  const studioEnv = createStudioEnvironment()
  scene.environment = studioEnv

  // 4. Studio Lighting Rig
  ambientLight = new THREE.AmbientLight(currentLighting.value.ambientColor, currentLighting.value.ambientIntensity)
  scene.add(ambientLight)

  keyLight = new THREE.DirectionalLight(currentLighting.value.keyColor, currentLighting.value.keyIntensity)
  keyLight.position.set(3.8, 5.8, 3.2)
  keyLight.castShadow = true
  keyLight.shadow.mapSize.width = 2048
  keyLight.shadow.mapSize.height = 2048
  keyLight.shadow.camera.near = 0.5
  keyLight.shadow.camera.far = 15
  keyLight.shadow.bias = -0.0003
  scene.add(keyLight)

  rimLight = new THREE.DirectionalLight(0xffeedd, 0.95)
  rimLight.position.set(-3.8, 3.2, -2.5)
  scene.add(rimLight)

  const underFill = new THREE.DirectionalLight(0xb87333, 0.35)
  underFill.position.set(0, -3.0, 2.0)
  scene.add(underFill)

  pointLight = new THREE.PointLight(currentLighting.value.pointColor, 1.4, 6)
  pointLight.position.set(0, 2.0, 2.2)
  scene.add(pointLight)

  // 5. Build Volumetric Organic Sculpted Loaf & Artisanal Tray
  buildShowcaseScene()

  // 6. Start Render Loop
  animate()
  isReady.value = true
}

function buildShowcaseScene() {
  showcaseGroup = new THREE.Group()
  scene.add(showcaseGroup)

  const textureLoader = new THREE.TextureLoader()

  // High-precision textures with 16x anisotropic filtering
  const crustTex = textureLoader.load('/images/textures/banana_bread_crust.jpg')
  crustTex.wrapS = THREE.ClampToEdgeWrapping
  crustTex.wrapT = THREE.ClampToEdgeWrapping
  crustTex.anisotropy = 16

  const crumbTex = textureLoader.load('/images/textures/banana_bread_crumb.jpg')
  crumbTex.wrapS = THREE.RepeatWrapping
  crumbTex.wrapT = THREE.RepeatWrapping
  crumbTex.repeat.set(2.0, 1.0)
  crumbTex.anisotropy = 16

  const kraftTex = textureLoader.load('/images/textures/kraft_cardboard.jpg')
  kraftTex.wrapS = THREE.RepeatWrapping
  kraftTex.wrapT = THREE.RepeatWrapping
  kraftTex.repeat.set(3.0, 1.5)
  kraftTex.anisotropy = 8

  // ──────────────────────────────────────────────────────────────────
  // A. Volumetric Organic Banana Bread Loaf (Subdivided & Beveled)
  // ──────────────────────────────────────────────────────────────────
  const loafWidth = 2.40
  const loafHeight = 0.95
  const loafDepth = 1.35
  const loafGeom = new THREE.BoxGeometry(loafWidth, loafHeight, loafDepth, 52, 28, 36)
  const pos = loafGeom.attributes.position
  const uvs = loafGeom.attributes.uv

  // Dynamic surface height calculation for topping adhesion
  const getSurfaceHeight = (targetX, targetZ) => {
    const nx = targetX / 1.20
    const nz = targetZ / 0.675
    const dome = Math.max(0, 1 - (nx * nx * 0.65 + nz * nz * 0.78))
    let y = 0.475 + dome * 0.42
    const crackWidth = 0.16
    if (Math.abs(targetZ) < crackWidth && Math.abs(targetX) < 0.95) {
      y -= (1 - Math.abs(targetZ) / crackWidth) * 0.09 * (1 - Math.abs(targetX) * 0.35)
    }
    return y + 0.08
  }

  for (let i = 0; i < pos.count; i++) {
    let x = pos.getX(i)
    let y = pos.getY(i)
    let z = pos.getZ(i)

    const normX = x / (loafWidth / 2)
    const normZ = z / (loafDepth / 2)

    // 1. Smoothly round all 4 vertical corners (eliminate boxy sharp 90-degree edges)
    const cornerFactor = Math.pow(Math.abs(normX), 4) + Math.pow(Math.abs(normZ), 4)
    if (cornerFactor > 0.65) {
      const shrink = 1 - (cornerFactor - 0.65) * 0.18
      x *= shrink
      z *= shrink
    }

    // 2. Top Face & Muffin-Top Rim Smoothing
    if (y > 0.1) {
      // Natural dome rise of the crown
      const dome = Math.max(0, 1 - (normX * normX * 0.65 + normZ * normZ * 0.78))
      y += dome * 0.42

      // Artisanal longitudinal crack (steam release split along the center ridge)
      const crackWidth = 0.16
      if (Math.abs(z) < crackWidth && Math.abs(x) < 0.95) {
        const dip = (1 - Math.abs(z) / crackWidth) * 0.09 * (1 - Math.abs(x) * 0.35)
        y -= dip
      }

      // Soft batter overflow over pan rim (muffin-top roll)
      if (y < 0.45 && y > 0.15) {
        const rimTaper = Math.sin((y - 0.15) / 0.30 * Math.PI) * 0.05
        x += Math.sign(x) * rimTaper
        z += Math.sign(z) * rimTaper
      }

      // Organic micro-waviness (fresh baked crumb texture)
      y += Math.sin(x * 12.0) * Math.cos(z * 15.0) * 0.012
    } else {
      // Lower loaf inside pan: slight inward draft angle (taper to base)
      const panTaper = 1.0 - (0.1 - y) * 0.06
      x *= panTaper
      z *= panTaper

      // Rounded bottom corners
      if (Math.abs(normX) > 0.82 && Math.abs(normZ) > 0.82) {
        y += 0.03
      }
    }

    // 3. Planar UV mapping for the top dome: gives seamless un-stretched crust
    if (y > 0.35) {
      const u = (x + loafWidth / 2) / loafWidth
      const v = (z + loafDepth / 2) / loafDepth
      uvs.setXY(i, u, v)
    }

    pos.setXYZ(i, x, y, z)
  }

  loafGeom.computeVertexNormals()
  uvs.needsUpdate = true

  // Multi-Material: Top Crust (with Clearcoat Glaze & Sheen) + Side/Bottom Crumb
  const topCrustMat = new THREE.MeshPhysicalMaterial({
    map: crustTex,
    color: 0xffffff,
    roughness: 0.54,
    metalness: 0.02,
    clearcoat: 0.38,
    clearcoatRoughness: 0.32,
    sheen: 0.45,
    sheenColor: new THREE.Color(0xe8a760),
    bumpMap: crustTex,
    bumpScale: 0.007,
  })

  const sideCrumbMat = new THREE.MeshPhysicalMaterial({
    map: crumbTex,
    color: 0xf5ddc2,
    roughness: 0.88,
    metalness: 0.0,
    bumpMap: crumbTex,
    bumpScale: 0.004,
  })

  const bottomMat = new THREE.MeshStandardMaterial({
    color: 0x8a542b,
    roughness: 0.95,
  })

  // BoxGeometry material group indices:
  // [0: +X, 1: -X, 2: +Y (Top), 3: -Y (Bottom), 4: +Z, 5: -Z]
  const loafMaterials = [
    sideCrumbMat,  // +X right side
    sideCrumbMat,  // -X left side
    topCrustMat,   // +Y top crust
    bottomMat,     // -Y bottom
    sideCrumbMat,  // +Z front side
    sideCrumbMat   // -Z back side
  ]

  const loafMesh = new THREE.Mesh(loafGeom, loafMaterials)
  loafMesh.position.y = 0.08
  loafMesh.castShadow = true
  loafMesh.receiveShadow = true
  showcaseGroup.add(loafMesh)

  // ──────────────────────────────────────────────────────────────────
  // B. Artisanal Corrugated Kraft Loaf Tray (Authentic Bakery Liner)
  // ──────────────────────────────────────────────────────────────────
  const trayWidth = 2.44
  const trayHeight = 0.44
  const trayDepth = 1.38
  const trayGeom = new THREE.BoxGeometry(trayWidth, trayHeight, trayDepth, 48, 8, 32)
  const trayPos = trayGeom.attributes.position

  for (let i = 0; i < trayPos.count; i++) {
    let x = trayPos.getX(i)
    let y = trayPos.getY(i)
    let z = trayPos.getZ(i)

    const normX = x / (trayWidth / 2)
    const normZ = z / (trayDepth / 2)

    // Round the 4 vertical tray corners
    const cornerFactor = Math.pow(Math.abs(normX), 4) + Math.pow(Math.abs(normZ), 4)
    if (cornerFactor > 0.65) {
      const shrink = 1 - (cornerFactor - 0.65) * 0.16
      x *= shrink
      z *= shrink
    }

    // Outward flare of the paper tray walls (draft angle)
    const flare = (y + trayHeight / 2) / trayHeight * 0.04
    x += Math.sign(x) * flare
    z += Math.sign(z) * flare

    // Subtle corrugated fluting along the exterior paper walls
    if (Math.abs(normZ) > 0.85) {
      z += Math.sin(x * 36.0) * 0.009
    }
    if (Math.abs(normX) > 0.85) {
      x += Math.sin(z * 36.0) * 0.009
    }

    trayPos.setXYZ(i, x, y, z)
  }
  trayGeom.computeVertexNormals()

  const trayMat = new THREE.MeshStandardMaterial({
    map: kraftTex,
    color: 0xc8a67e,
    roughness: 0.82,
    metalness: 0.03,
  })

  const trayMesh = new THREE.Mesh(trayGeom, trayMat)
  trayMesh.position.y = -0.23
  trayMesh.castShadow = true
  trayMesh.receiveShadow = true
  showcaseGroup.add(trayMesh)

  // Delicate Kraft Rim Fold Lip
  const rimGeom = new THREE.BoxGeometry(2.47, 0.035, 1.41, 32, 2, 24)
  const rimMat = new THREE.MeshStandardMaterial({
    color: 0xb59168,
    roughness: 0.78,
  })
  const rimMesh = new THREE.Mesh(rimGeom, rimMat)
  rimMesh.position.y = -0.015
  rimMesh.castShadow = true
  showcaseGroup.add(rimMesh)

  // Gold Foil ABCDips Artisan Label on the front of the tray
  const badgeGeom = new THREE.PlaneGeometry(0.72, 0.22)
  const badgeCanvas = document.createElement('canvas')
  badgeCanvas.width = 256
  badgeCanvas.height = 80
  const bCtx = badgeCanvas.getContext('2d')
  bCtx.fillStyle = '#1A120C'
  bCtx.fillRect(0, 0, 256, 80)
  bCtx.strokeStyle = '#D9A876'
  bCtx.lineWidth = 4
  bCtx.strokeRect(6, 6, 244, 68)
  bCtx.fillStyle = '#E2C08A'
  bCtx.font = 'bold 22px sans-serif'
  bCtx.textAlign = 'center'
  bCtx.textBaseline = 'middle'
  bCtx.fillText('ABCDIPS & TREATS', 128, 30)
  bCtx.font = 'italic 16px serif'
  bCtx.fillStyle = '#D9A876'
  bCtx.fillText('Classic Banana Bread', 128, 54)

  const badgeTex = new THREE.CanvasTexture(badgeCanvas)
  const badgeMat = new THREE.MeshStandardMaterial({
    map: badgeTex,
    roughness: 0.38,
    metalness: 0.55,
  })
  const badgeMesh = new THREE.Mesh(badgeGeom, badgeMat)
  badgeMesh.position.set(0, -0.23, trayDepth / 2 + 0.026)
  showcaseGroup.add(badgeMesh)

  // ──────────────────────────────────────────────────────────────────
  // C. Physical 3D Toppings (Toasted Almond Slices & Chocolate Morsels)
  // ──────────────────────────────────────────────────────────────────
  // 1. Toasted Almond Slices (flaked thin oval discs)
  const almondGeom = new THREE.CylinderGeometry(0.105, 0.10, 0.018, 14)
  almondGeom.scale(1.45, 1.0, 0.75) // Oval flake

  const almondMat = new THREE.MeshStandardMaterial({
    color: 0xf6e3cc,
    roughness: 0.52,
    metalness: 0.02,
  })

  const almondRimMat = new THREE.MeshStandardMaterial({
    color: 0x8a461b, // toasted caramelized rim
    roughness: 0.6,
  })

  // Toasted Almond Seeds across the crown
  const almondSeeds = [
    [-0.82, -0.15, 0.35], [-0.60, 0.22, -0.42], [-0.38, -0.25, 0.55],
    [-0.15, 0.18, -0.25], [0.10, -0.22, 0.48], [0.35, 0.24, -0.38],
    [0.60, -0.16, 0.42], [0.82, 0.18, -0.52], [-0.70, 0.32, 0.85],
    [0.48, -0.32, -0.72], [-0.22, -0.35, 0.32], [0.22, 0.35, -0.48],
    [-0.45, 0.0, 0.15], [0.0, 0.02, -0.12], [0.42, 0.05, 0.25],
    [-0.92, 0.08, 0.5], [0.72, 0.05, -0.65], [-0.05, -0.18, 0.4],
    [-0.30, 0.12, 0.2], [0.30, -0.12, -0.3]
  ]

  almondSeeds.forEach(([ax, az, rot]) => {
    const ay = getSurfaceHeight(ax, az)
    const almond = new THREE.Mesh(almondGeom, [almondRimMat, almondMat, almondMat])
    almond.position.set(ax, ay + 0.015, az)
    almond.rotation.set(0.18 + Math.sin(ax * 3) * 0.15, rot, 0.12 + Math.cos(az * 3) * 0.15)
    almond.castShadow = true
    showcaseGroup.add(almond)
  })

  // 2. Dark Chocolate Morsels
  const chocoGeom = new THREE.ConeGeometry(0.048, 0.065, 6)
  const chocoMat = new THREE.MeshPhysicalMaterial({
    color: 0x180a04,
    roughness: 0.20,
    metalness: 0.15,
    clearcoat: 0.75,
    clearcoatRoughness: 0.22,
  })

  const chocoSeeds = [
    [-0.68, -0.06], [-0.48, 0.14], [-0.28, -0.08],
    [-0.08, 0.06], [0.15, -0.05], [0.38, 0.08],
    [0.58, -0.07], [0.78, 0.06], [-0.35, -0.22],
    [0.25, 0.24], [-0.55, 0.25], [0.65, -0.22],
    [-0.85, 0.16], [0.05, -0.26], [-0.18, 0.22],
    [0.48, -0.12]
  ]

  chocoSeeds.forEach(([cx, cz]) => {
    const cy = getSurfaceHeight(cx, cz)
    const chip = new THREE.Mesh(chocoGeom, chocoMat)
    chip.position.set(cx, cy + 0.018, cz)
    chip.rotation.set(-0.25, Math.random() * 3.14, 0.18)
    chip.castShadow = true
    showcaseGroup.add(chip)
  })

  // ──────────────────────────────────────────────────────────────────
  // D. Atmospheric Golden Aroma Steam Particles
  // ──────────────────────────────────────────────────────────────────
  const particleCount = 28
  const particleGeom = new THREE.BufferGeometry()
  const particlePositions = new Float32Array(particleCount * 3)
  const particleScales = new Float32Array(particleCount)

  for (let i = 0; i < particleCount; i++) {
    particlePositions[i * 3] = (Math.random() - 0.5) * 2.2
    particlePositions[i * 3 + 1] = 0.5 + Math.random() * 1.5
    particlePositions[i * 3 + 2] = (Math.random() - 0.5) * 1.4
    particleScales[i] = 0.015 + Math.random() * 0.02
  }
  particleGeom.setAttribute('position', new THREE.BufferAttribute(particlePositions, 3))

  const particleMat = new THREE.PointsMaterial({
    color: 0xE2C08A,
    size: 0.04,
    transparent: true,
    opacity: 0.45,
    blending: THREE.AdditiveBlending,
  })
  steamParticles = new THREE.Points(particleGeom, particleMat)
  showcaseGroup.add(steamParticles)

  // ──────────────────────────────────────────────────────────────────
  // E. Artisanal Acacia Wood Turntable Pedestal
  // ──────────────────────────────────────────────────────────────────
  const boardGeom = new THREE.CylinderGeometry(2.05, 2.10, 0.13, 64)
  const boardMat = new THREE.MeshStandardMaterial({
    color: 0x27170e,
    roughness: 0.72,
    metalness: 0.05,
  })

  const boardMesh = new THREE.Mesh(boardGeom, boardMat)
  boardMesh.position.y = -0.52
  boardMesh.receiveShadow = true
  showcaseGroup.add(boardMesh)

  // Pedestal Inset Brass Ring
  const ringGeom = new THREE.TorusGeometry(2.03, 0.014, 16, 64)
  ringGeom.rotateX(Math.PI / 2)
  const ringMat = new THREE.MeshStandardMaterial({
    color: 0xd9a876,
    roughness: 0.32,
    metalness: 0.85,
  })
  const ringMesh = new THREE.Mesh(ringGeom, ringMat)
  ringMesh.position.y = -0.455
  showcaseGroup.add(ringMesh)

  // Soft Ambient Contact Shadow Under Pedestal
  const shadowCanvas = document.createElement('canvas')
  shadowCanvas.width = 128
  shadowCanvas.height = 128
  const sCtx = shadowCanvas.getContext('2d')
  const sGrad = sCtx.createRadialGradient(64, 64, 12, 64, 64, 64)
  sGrad.addColorStop(0, 'rgba(0,0,0,0.88)')
  sGrad.addColorStop(0.5, 'rgba(0,0,0,0.42)')
  sGrad.addColorStop(1, 'rgba(0,0,0,0)')
  sCtx.fillStyle = sGrad
  sCtx.fillRect(0, 0, 128, 128)

  const shadowTex = new THREE.CanvasTexture(shadowCanvas)
  const shadowPlane = new THREE.Mesh(
    new THREE.PlaneGeometry(4.6, 4.6),
    new THREE.MeshBasicMaterial({ map: shadowTex, transparent: true, opacity: 0.88 })
  )
  shadowPlane.rotation.x = -Math.PI / 2
  shadowPlane.position.y = -0.59
  showcaseGroup.add(shadowPlane)
}

function handleMouseDown(e) {
  isDragging = true
  previousMouse = { x: e.clientX, y: e.clientY }
  isAutoRotating.value = false
}

function handleMouseMove(e) {
  if (pointLight && canvasContainerRef.value) {
    const rect = canvasContainerRef.value.getBoundingClientRect()
    const nx = ((e.clientX - rect.left) / rect.width) * 2 - 1
    const ny = -((e.clientY - rect.top) / rect.height) * 2 + 1
    pointLight.position.x = nx * 2.0
    pointLight.position.y = ny * 1.5 + 1.2
  }

  if (!isDragging) return

  const deltaX = e.clientX - previousMouse.x
  const deltaY = e.clientY - previousMouse.y

  targetRotation.y += deltaX * 0.008
  targetRotation.x = Math.max(-0.2, Math.min(0.65, targetRotation.x + deltaY * 0.006))

  previousMouse = { x: e.clientX, y: e.clientY }
}

function handleMouseUp() {
  isDragging = false
}

function handleMouseLeave() {
  isDragging = false
}

function handleTouchStart(e) {
  if (e.touches.length > 0) {
    isDragging = true
    previousMouse = { x: e.touches[0].clientX, y: e.touches[0].clientY }
    isAutoRotating.value = false
  }
}

function handleTouchMove(e) {
  if (!isDragging || e.touches.length === 0) return

  const deltaX = e.touches[0].clientX - previousMouse.x
  const deltaY = e.touches[0].clientY - previousMouse.y

  targetRotation.y += deltaX * 0.009
  targetRotation.x = Math.max(-0.2, Math.min(0.65, targetRotation.x + deltaY * 0.007))

  previousMouse = { x: e.touches[0].clientX, y: e.touches[0].clientY }
}

function handleTouchEnd() {
  isDragging = false
}

function resetCamera() {
  targetRotation = { x: 0.26, y: -0.42 }
}

function toggleAutoRotate() {
  isAutoRotating.value = !isAutoRotating.value
}

function animate() {
  rafId = requestAnimationFrame(animate)
  animTime += 0.016

  if (isAutoRotating.value && !isDragging) {
    targetRotation.y += 0.006
  }

  // Smooth spring damping on rotation
  currentRotation.x += (targetRotation.x - currentRotation.x) * 0.1
  currentRotation.y += (targetRotation.y - currentRotation.y) * 0.1

  if (showcaseGroup) {
    showcaseGroup.rotation.x = currentRotation.x
    showcaseGroup.rotation.y = currentRotation.y

    // Subtle natural breathing float
    showcaseGroup.position.y = Math.sin(animTime * 1.8) * 0.018
  }

  // Drift atmospheric aroma particles
  if (steamParticles) {
    const pos = steamParticles.geometry.attributes.position
    for (let i = 0; i < pos.count; i++) {
      let py = pos.getY(i) + 0.003
      if (py > 2.0) py = 0.5
      pos.setY(i, py)
    }
    pos.needsUpdate = true
  }

  if (renderer && scene && camera) {
    renderer.render(scene, camera)
  }
}

function onWindowResize() {
  if (!canvasContainerRef.value || !renderer || !camera) return
  const width = canvasContainerRef.value.clientWidth
  const height = canvasContainerRef.value.clientHeight
  camera.aspect = width / height
  camera.updateProjectionMatrix()
  renderer.setSize(width, height)
}

onMounted(() => {
  initThree()
  window.addEventListener('resize', onWindowResize)
})

onUnmounted(() => {
  window.removeEventListener('resize', onWindowResize)
  if (rafId) cancelAnimationFrame(rafId)
  if (renderer) {
    renderer.dispose()
  }
})
</script>

<style scoped>
canvas {
  outline: none;
  touch-action: none;
}
</style>
