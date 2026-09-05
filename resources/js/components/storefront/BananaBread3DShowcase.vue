<template>
  <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="relative bg-gradient-to-br from-[#1C1410] via-[#160E0A] to-[#100906] text-[#FBF3E7] rounded-3xl overflow-hidden border border-[#C08E5D]/30 shadow-2xl">
      
      <!-- Ambient Studio Lighting Aura -->
      <div class="absolute -top-32 -left-32 w-80 h-80 rounded-full blur-3xl pointer-events-none transition-all duration-700"
           :style="{ background: currentLighting.auraLeft }" />
      <div class="absolute -bottom-32 -right-32 w-80 h-80 rounded-full blur-3xl pointer-events-none transition-all duration-700"
           :style="{ background: currentLighting.auraRight }" />
      <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_center,rgba(217,168,118,0.05)_0%,transparent_70%)] pointer-events-none" />

      <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-center p-6 sm:p-10 lg:p-14 relative z-10">
        
        <!-- Left: Editorial Narrative & Craft Telemetry -->
        <div class="lg:col-span-5 space-y-6 text-left">
          
          <!-- Badge -->
          <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-[#D9A876]/15 border border-[#C08E5D]/30 text-xs font-bold text-[#E2C08A] tracking-wider uppercase backdrop-blur-md">
            <Sparkles class="w-3.5 h-3.5 text-[#E2C08A] animate-pulse" />
            <span>Interactive 3D Craft Showcase</span>
          </div>

          <!-- Title & Editorial Description -->
          <div class="space-y-3">
            <h2 class="text-3xl sm:text-4xl lg:text-5xl font-black text-[#FBF3E7] tracking-tight leading-[1.15]">
              Classic Cavendish <br />
              <span class="font-['Caveat'] text-[#E2C08A] font-normal text-4xl sm:text-5xl block mt-0.5">
                Banana Bread Loaf
              </span>
            </h2>
            <p class="text-sm sm:text-base text-[#FBF3E7]/80 leading-relaxed max-w-lg">
              Rotate, inspect, and experience our signature pastry in real-time 3D. Handcrafted with Cavite-harvested Cavendish bananas at peak sweetness, slow-infused with 100% pure creamery butter.
            </p>
          </div>

          <!-- Craft Telemetry Specs (Bento Rail) -->
          <div class="grid grid-cols-3 gap-3 pt-1">
            <div class="p-3 rounded-2xl bg-white/5 border border-[#C08E5D]/20 backdrop-blur-sm text-center">
              <span class="text-[10px] uppercase font-mono tracking-wider text-[#E2C08A]/70 block">Butter Fat</span>
              <span class="text-base sm:text-lg font-black font-mono text-[#FBF3E7]">82% Pure</span>
            </div>
            <div class="p-3 rounded-2xl bg-white/5 border border-[#C08E5D]/20 backdrop-blur-sm text-center">
              <span class="text-[10px] uppercase font-mono tracking-wider text-[#E2C08A]/70 block">Bake Temp</span>
              <span class="text-base sm:text-lg font-black font-mono text-[#FBF3E7]">175°C Hearth</span>
            </div>
            <div class="p-3 rounded-2xl bg-white/5 border border-[#C08E5D]/20 backdrop-blur-sm text-center">
              <span class="text-[10px] uppercase font-mono tracking-wider text-[#E2C08A]/70 block">Prep Time</span>
              <span class="text-base sm:text-lg font-black font-mono text-[#FBF3E7]">45 Mins</span>
            </div>
          </div>

          <!-- Tasting Note Pills -->
          <div class="space-y-2 pt-1">
            <div class="text-xs font-bold uppercase tracking-wider text-[#E2C08A]/80 font-mono">Tasting Notes &amp; Ingredients</div>
            <div class="flex flex-wrap gap-2">
              <span v-for="tag in tastingNotes" :key="tag"
                    class="px-3 py-1 rounded-xl bg-[#D9A876]/10 border border-[#C08E5D]/25 text-xs font-semibold text-[#FBF3E7] flex items-center gap-1.5">
                <span class="w-1.5 h-1.5 rounded-full bg-[#E2C08A]" />
                <span>{{ tag }}</span>
              </span>
            </div>
          </div>

          <!-- Action Row -->
          <div class="flex flex-wrap items-center gap-4 pt-2">
            <RouterLink to="/products/classic-cavendish-banana-bread-loaf"
                        class="bg-gradient-to-r from-[#D9A876] to-[#C08E5D] text-[#1C1410] px-7 py-3.5 rounded-2xl font-extrabold text-sm hover:opacity-95 shadow-lg shadow-[#C08E5D]/20 hover:scale-[1.02] active:scale-[0.98] transition-all flex items-center gap-2">
              <ShoppingBag class="w-4 h-4 text-[#1C1410]" />
              <span>Order Fresh Loaf • ₱280.00</span>
            </RouterLink>
            <RouterLink to="/shop?category=banana-bread"
                        class="px-5 py-3.5 rounded-2xl font-bold text-xs sm:text-sm text-[#E2C08A] hover:text-[#FBF3E7] hover:bg-white/5 border border-[#C08E5D]/30 transition-all flex items-center gap-1.5">
              <span>All Variations</span>
              <ArrowRight class="w-3.5 h-3.5" />
            </RouterLink>
          </div>

        </div>

        <!-- Right: Real-time 3D WebGL Interactive Viewport -->
        <div class="lg:col-span-7 flex flex-col items-center justify-center relative">
          
          <!-- Viewport Card -->
          <div class="w-full aspect-[4/3] sm:aspect-[16/11] rounded-3xl bg-black/40 border border-[#C08E5D]/30 backdrop-blur-xl relative overflow-hidden shadow-inner group">
            
            <!-- Three.js Canvas Container -->
            <div ref="canvasContainerRef"
                 class="w-full h-full cursor-grab active:cursor-grabbing select-none"
                 @mousedown="onMouseDown"
                 @mousemove="onMouseMove"
                 @mouseup="onMouseUp"
                 @mouseleave="onMouseLeave"
                 @touchstart="onTouchStart"
                 @touchmove="onTouchMove"
                 @touchend="onTouchEnd" />

            <!-- Interactive Cursor Drag Hint Overlay -->
            <div class="absolute bottom-4 left-4 z-20 pointer-events-none flex items-center gap-2 bg-[#1C1410]/80 backdrop-blur-md px-3 py-1.5 rounded-full border border-[#C08E5D]/30 text-[11px] font-mono text-[#E2C08A]">
              <Move3d class="w-3.5 h-3.5 animate-spin" style="animation-duration: 6s;" />
              <span>Click &amp; Drag to Rotate 360°</span>
            </div>

            <!-- Auto-Rotation Status Indicator -->
            <button class="absolute top-4 right-4 z-20 bg-[#1C1410]/80 backdrop-blur-md px-3 py-1.5 rounded-full border border-[#C08E5D]/30 text-[11px] font-bold text-[#FBF3E7] hover:bg-[#2A1C13] transition-all flex items-center gap-1.5"
                    @click="toggleAutoRotate">
              <RotateCcw class="w-3 h-3 text-[#E2C08A]" :class="isAutoRotating ? 'animate-spin' : ''" style="animation-duration: 8s;" />
              <span>{{ isAutoRotating ? 'Auto Orbit ON' : 'Paused' }}</span>
            </button>

            <!-- Reset Camera Button -->
            <button class="absolute bottom-4 right-4 z-20 bg-[#1C1410]/80 backdrop-blur-md px-3 py-1.5 rounded-full border border-[#C08E5D]/30 text-[11px] font-bold text-[#E2C08A] hover:text-[#FBF3E7] hover:bg-[#2A1C13] transition-all flex items-center gap-1.5"
                    @click="resetCamera">
              <Compass class="w-3.5 h-3.5" />
              <span>Reset View</span>
            </button>

            <!-- Loading Spinner Placeholder -->
            <div v-if="!isReady"
                 class="absolute inset-0 z-30 flex flex-col items-center justify-center bg-[#140D09]/90 backdrop-blur-md">
              <div class="w-10 h-10 border-3 border-[#C08E5D]/30 border-t-[#D9A876] rounded-full animate-spin mb-3" />
              <p class="text-xs font-mono text-[#E2C08A] uppercase tracking-widest">Rendering 3D Loaf...</p>
            </div>
          </div>

          <!-- Studio Lighting Mode Strip (Like Huenics Lighting Switcher) -->
          <div class="mt-4 w-full flex flex-wrap items-center justify-between gap-3 p-2 bg-[#1C1410]/70 rounded-2xl border border-[#C08E5D]/25 backdrop-blur-md">
            <span class="text-xs font-bold uppercase tracking-wider text-[#E2C08A]/80 font-mono px-2 flex items-center gap-1.5">
              <Sun class="w-3.5 h-3.5 text-[#E2C08A]" />
              <span>Studio Lighting:</span>
            </span>

            <div class="flex items-center gap-1.5 overflow-x-auto">
              <button v-for="mode in lightingModes" :key="mode.id"
                      @click="setLightingMode(mode.id)"
                      :class="[
                        'px-3 py-1.5 rounded-xl text-xs font-bold transition-all whitespace-nowrap flex items-center gap-1.5',
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
import { Sparkles, ShoppingBag, ArrowRight, RotateCcw, Move3d, Compass, Sun, Flame, Moon } from 'lucide-vue-next'
import * as THREE from 'three'

const canvasContainerRef = ref(null)
const isReady = ref(false)
const isAutoRotating = ref(true)
const activeLightingMode = ref('oven')

const tastingNotes = [
  'Roasted Walnut Crunch',
  'Caramelized Crust Burst',
  'Golden Cavendish Sweetness',
  'Real Buttercrumb Texture',
  'Organic Cinnamon Hint'
]

const lightingModes = [
  { id: 'oven', label: 'Oven Glow (Warm)', icon: Flame },
  { id: 'daylight', label: 'Studio Sun', icon: Sun },
  { id: 'espresso', label: 'Dark Hearth', icon: Moon },
]

const currentLighting = computed(() => {
  if (activeLightingMode.value === 'daylight') {
    return {
      auraLeft: 'rgba(255, 230, 180, 0.18)',
      auraRight: 'rgba(200, 220, 255, 0.12)',
      keyColor: 0xfff4e6,
      ambientColor: 0xfbf3e7,
      intensity: 1.4
    }
  }
  if (activeLightingMode.value === 'espresso') {
    return {
      auraLeft: 'rgba(192, 142, 93, 0.12)',
      auraRight: 'rgba(92, 58, 34, 0.25)',
      keyColor: 0xd9a876,
      ambientColor: 0x2d1b10,
      intensity: 0.9
    }
  }
  // Default: oven glow
  return {
    auraLeft: 'rgba(217, 168, 118, 0.22)',
    auraRight: 'rgba(192, 142, 93, 0.22)',
    keyColor: 0xffaa55,
    ambientColor: 0x4a2a14,
    intensity: 1.2
  }
})

// Three.js instances
let scene, camera, renderer, loafGroup, pointLight, keyLight, ambientLight, steamParticles
let rafId = null
let isDragging = false
let previousMousePosition = { x: 0, y: 0 }
let targetRotation = { x: 0.2, y: -0.4 }
let currentRotation = { x: 0.2, y: -0.4 }
let floatTime = 0

function initThree() {
  if (!canvasContainerRef.value) return

  const container = canvasContainerRef.value
  const width = container.clientWidth
  const height = container.clientHeight

  // 1. Scene
  scene = new THREE.Scene()

  // 2. Camera
  camera = new THREE.PerspectiveCamera(42, width / height, 0.1, 100)
  camera.position.set(0, 1.2, 5.2)

  // 3. Renderer
  renderer = new THREE.WebGLRenderer({ antialias: true, alpha: true, powerPreference: 'high-performance' })
  renderer.setSize(width, height)
  renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2))
  renderer.toneMapping = THREE.ACESFilmicToneMapping
  renderer.toneMappingExposure = 1.15
  renderer.shadowMap.enabled = true
  renderer.shadowMap.type = THREE.PCFSoftShadowMap

  container.appendChild(renderer.domElement)

  // 4. Lights (High-end Studio Setup like Huenics)
  ambientLight = new THREE.AmbientLight(currentLighting.value.ambientColor, 1.2)
  scene.add(ambientLight)

  keyLight = new THREE.DirectionalLight(currentLighting.value.keyColor, currentLighting.value.intensity)
  keyLight.position.set(4, 5, 4)
  keyLight.castShadow = true
  keyLight.shadow.mapSize.width = 1024
  keyLight.shadow.mapSize.height = 1024
  scene.add(keyLight)

  // Rim light for luxury edge definition
  const rimLight = new THREE.DirectionalLight(0xd9c0a3, 0.8)
  rimLight.position.set(-4, 3, -3)
  scene.add(rimLight)

  // Cursor Follower Point Light (tracks mouse across the loaf)
  pointLight = new THREE.PointLight(0xffd59e, 1.5, 8)
  pointLight.position.set(0, 2, 2)
  scene.add(pointLight)

  // 5. Build 3D Banana Bread Loaf
  buildBananaBreadMesh()

  // 6. Build Rising Aroma Steam Embers
  buildAromaParticles()

  // 7. Contact Shadow Ground Plane
  buildContactShadow()

  isReady.value = true
  animate()

  window.addEventListener('resize', onWindowResize)
}

function createCaramelizedTexture() {
  const canvas = document.createElement('canvas')
  canvas.width = 1024
  canvas.height = 512
  const ctx = canvas.getContext('2d')

  // Warm golden baked loaf gradient
  const grad = ctx.createLinearGradient(0, 0, 0, 512)
  grad.addColorStop(0, '#5C2E0B')    // Dark caramelized top crust
  grad.addColorStop(0.25, '#8C481A') // Golden crust
  grad.addColorStop(0.5, '#A65D28')  // Warm amber crust
  grad.addColorStop(0.85, '#B87333') // Lower crust
  grad.addColorStop(1, '#5C2E0B')    // Bottom base
  ctx.fillStyle = grad
  ctx.fillRect(0, 0, 1024, 512)

  // Add organic crackled texture noise
  for (let i = 0; i < 4000; i++) {
    const x = Math.random() * 1024
    const y = Math.random() * 512
    const radius = Math.random() * 2.5
    ctx.fillStyle = Math.random() > 0.6 ? '#421E06' : '#C88846'
    ctx.beginPath()
    ctx.arc(x, y, radius, 0, Math.PI * 2)
    ctx.fill()
  }

  // Top fissure crack line down the center (signature banana bread oven burst!)
  ctx.strokeStyle = '#D9A876'
  ctx.lineWidth = 14
  ctx.beginPath()
  ctx.moveTo(120, 256)
  for (let x = 120; x < 900; x += 30) {
    const y = 256 + (Math.sin(x * 0.05) * 16) + (Math.random() * 10 - 5)
    ctx.lineTo(x, y)
  }
  ctx.stroke()

  ctx.strokeStyle = '#FBF3E7'
  ctx.lineWidth = 4
  ctx.stroke()

  const texture = new THREE.CanvasTexture(canvas)
  texture.wrapS = THREE.RepeatWrapping
  texture.wrapT = THREE.RepeatWrapping
  return texture
}

function buildBananaBreadMesh() {
  loafGroup = new THREE.Group()

  const loafTexture = createCaramelizedTexture()

  // 1. Artisanal Loaf Body (Rounded, tapered baked loaf)
  const loafGeo = new THREE.BoxGeometry(3.2, 1.4, 1.6, 16, 16, 16)
  
  // Deform vertices to create natural domed crown & tapered bakery loaf
  const pos = loafGeo.attributes.position
  for (let i = 0; i < pos.count; i++) {
    const x = pos.getX(i)
    const y = pos.getY(i)
    const z = pos.getZ(i)

    // Domed top crown
    if (y > 0) {
      const distFromCenter = Math.sqrt((x / 1.6) ** 2 + (z / 0.8) ** 2)
      pos.setY(i, y + Math.max(0, 0.45 * (1 - distFromCenter * 0.8)))
    }
    // Slight bottom taper (bread pan shape)
    if (y < 0) {
      pos.setX(i, x * 0.92)
      pos.setZ(i, z * 0.92)
    }
  }
  loafGeo.computeVertexNormals()

  const loafMat = new THREE.MeshStandardMaterial({
    map: loafTexture,
    roughness: 0.7,
    metalness: 0.05,
    bumpMap: loafTexture,
    bumpScale: 0.04
  })

  const loafMesh = new THREE.Mesh(loafGeo, loafMat)
  loafMesh.castShadow = true
  loafMesh.receiveShadow = true
  loafGroup.add(loafMesh)

  // 2. Caramelized Banana Medallions on the Crown
  const bananaGeo = new THREE.CylinderGeometry(0.24, 0.24, 0.05, 24)
  const bananaMat = new THREE.MeshStandardMaterial({
    color: 0xE5B869,
    roughness: 0.4,
    metalness: 0.1
  })

  const bananaPositions = [
    { x: -0.9, y: 0.92, z: 0.1, rotZ: 0.1, rotX: 0.2 },
    { x: -0.3, y: 0.98, z: -0.15, rotZ: -0.15, rotX: -0.1 },
    { x: 0.35, y: 0.96, z: 0.12, rotZ: 0.2, rotX: 0.15 },
    { x: 0.95, y: 0.90, z: -0.08, rotZ: -0.1, rotX: -0.2 }
  ]

  bananaPositions.forEach(p => {
    const banana = new THREE.Mesh(bananaGeo, bananaMat)
    banana.position.set(p.x, p.y, p.z)
    banana.rotation.z = p.rotZ
    banana.rotation.x = p.rotX
    banana.castShadow = true
    loafGroup.add(banana)
  })

  // 3. Crumbled Walnut & Choco Chunks along the Fissure
  const walnutGeo = new THREE.DodecahedronGeometry(0.08, 0)
  const walnutMat = new THREE.MeshStandardMaterial({ color: 0x4D2810, roughness: 0.9 })
  const chocoMat = new THREE.MeshStandardMaterial({ color: 0x221108, roughness: 0.3 })

  for (let i = 0; i < 22; i++) {
    const isWalnut = Math.random() > 0.4
    const crumb = new THREE.Mesh(walnutGeo, isWalnut ? walnutMat : chocoMat)
    const scale = 0.5 + Math.random() * 0.8
    crumb.scale.set(scale, scale, scale)
    crumb.position.set(
      (Math.random() - 0.5) * 2.5,
      0.88 + Math.random() * 0.14,
      (Math.random() - 0.5) * 0.6
    )
    crumb.rotation.set(Math.random() * Math.PI, Math.random() * Math.PI, 0)
    loafGroup.add(crumb)
  }

  // 4. Bakery Parchment Paper Liner (Crinkled rustic paper wrap)
  const paperGeo = new THREE.PlaneGeometry(3.6, 2.2, 8, 8)
  const paperMat = new THREE.MeshStandardMaterial({
    color: 0xEDE2CF,
    roughness: 0.95,
    side: THREE.DoubleSide
  })
  const paper = new THREE.Mesh(paperGeo, paperMat)
  paper.rotation.x = -Math.PI / 2
  paper.position.y = -0.72
  paper.receiveShadow = true
  loafGroup.add(paper)

  loafGroup.position.y = 0.1
  scene.add(loafGroup)
}

function buildAromaParticles() {
  const particleCount = 35
  const geometry = new THREE.BufferGeometry()
  const positions = new Float32Array(particleCount * 3)

  for (let i = 0; i < particleCount * 3; i += 3) {
    positions[i] = (Math.random() - 0.5) * 2.2      // X
    positions[i + 1] = 0.8 + Math.random() * 2.0     // Y
    positions[i + 2] = (Math.random() - 0.5) * 1.2  // Z
  }

  geometry.setAttribute('position', new THREE.BufferAttribute(positions, 3))

  const material = new THREE.PointsMaterial({
    color: 0xE2C08A,
    size: 0.06,
    transparent: true,
    opacity: 0.55,
    blending: THREE.AdditiveBlending
  })

  steamParticles = new THREE.Points(geometry, material)
  scene.add(steamParticles)
}

function buildContactShadow() {
  // Soft ambient shadow plane beneath the loaf
  const shadowGeo = new THREE.PlaneGeometry(5.5, 3.5)
  
  const canvas = document.createElement('canvas')
  canvas.width = 256
  canvas.height = 256
  const ctx = canvas.getContext('2d')
  const grad = ctx.createRadialGradient(128, 128, 10, 128, 128, 120)
  grad.addColorStop(0, 'rgba(18, 10, 5, 0.75)')
  grad.addColorStop(0.5, 'rgba(18, 10, 5, 0.25)')
  grad.addColorStop(1, 'rgba(18, 10, 5, 0)')
  ctx.fillStyle = grad
  ctx.fillRect(0, 0, 256, 256)

  const shadowTexture = new THREE.CanvasTexture(canvas)
  const shadowMat = new THREE.MeshBasicMaterial({
    map: shadowTexture,
    transparent: true,
    opacity: 0.8,
    depthWrite: false
  })

  const shadowPlane = new THREE.Mesh(shadowGeo, shadowMat)
  shadowPlane.rotation.x = -Math.PI / 2
  shadowPlane.position.y = -0.74
  scene.add(shadowPlane)
}

function animate() {
  rafId = requestAnimationFrame(animate)

  floatTime += 0.02

  // 1. Smooth rotation interpolation (Damped inertia)
  const factor = 0.08
  currentRotation.x += (targetRotation.x - currentRotation.x) * factor
  currentRotation.y += (targetRotation.y - currentRotation.y) * factor

  if (loafGroup) {
    // Idle auto-rotation
    if (isAutoRotating.value && !isDragging) {
      targetRotation.y += 0.005
    }

    loafGroup.rotation.x = currentRotation.x
    loafGroup.rotation.y = currentRotation.y

    // Floating hover levitation
    loafGroup.position.y = 0.1 + Math.sin(floatTime) * 0.06
  }

  // 2. Animate steam aroma particles rising
  if (steamParticles) {
    const pos = steamParticles.geometry.attributes.position
    for (let i = 1; i < pos.count * 3; i += 3) {
      pos.array[i] += 0.006 // rise
      if (pos.array[i] > 3.0) {
        pos.array[i] = 0.9
      }
    }
    pos.needsUpdate = true
  }

  if (renderer && scene && camera) {
    renderer.render(scene, camera)
  }
}

// Mouse / Touch Interaction Controls
function onMouseDown(e) {
  isDragging = true
  previousMousePosition = { x: e.clientX, y: e.clientY }
}

function onMouseMove(e) {
  // Update point light to follow cursor (like Huenics studio showcase)
  if (canvasContainerRef.value && pointLight) {
    const rect = canvasContainerRef.value.getBoundingClientRect()
    const normX = ((e.clientX - rect.left) / rect.width) * 2 - 1
    const normY = -((e.clientY - rect.top) / rect.height) * 2 + 1
    pointLight.position.x = normX * 3
    pointLight.position.y = normY * 2 + 1
  }

  if (!isDragging) return

  const deltaX = e.clientX - previousMousePosition.x
  const deltaY = e.clientY - previousMousePosition.y

  targetRotation.y += deltaX * 0.008
  targetRotation.x += deltaY * 0.008

  // Clamp pitch rotation so loaf doesn't flip completely upside down
  targetRotation.x = Math.max(-0.6, Math.min(0.8, targetRotation.x))

  previousMousePosition = { x: e.clientX, y: e.clientY }
}

function onMouseUp() {
  isDragging = false
}

function onMouseLeave() {
  isDragging = false
}

function onTouchStart(e) {
  if (e.touches.length === 1) {
    isDragging = true
    previousMousePosition = { x: e.touches[0].clientX, y: e.touches[0].clientY }
  }
}

function onTouchMove(e) {
  if (!isDragging || e.touches.length !== 1) return

  const deltaX = e.touches[0].clientX - previousMousePosition.x
  const deltaY = e.touches[0].clientY - previousMousePosition.y

  targetRotation.y += deltaX * 0.01
  targetRotation.x += deltaY * 0.01
  targetRotation.x = Math.max(-0.6, Math.min(0.8, targetRotation.x))

  previousMousePosition = { x: e.touches[0].clientX, y: e.touches[0].clientY }
}

function onTouchEnd() {
  isDragging = false
}

function toggleAutoRotate() {
  isAutoRotating.value = !isAutoRotating.value
}

function resetCamera() {
  targetRotation.x = 0.2
  targetRotation.y = -0.4
}

function setLightingMode(modeId) {
  activeLightingMode.value = modeId
  if (!keyLight || !ambientLight) return

  keyLight.color.setHex(currentLighting.value.keyColor)
  keyLight.intensity = currentLighting.value.intensity
  ambientLight.color.setHex(currentLighting.value.ambientColor)
}

function onWindowResize() {
  if (!canvasContainerRef.value || !camera || !renderer) return
  const width = canvasContainerRef.value.clientWidth
  const height = canvasContainerRef.value.clientHeight
  camera.aspect = width / height
  camera.updateProjectionMatrix()
  renderer.setSize(width, height)
}

onMounted(() => {
  initThree()
})

onUnmounted(() => {
  if (rafId) cancelAnimationFrame(rafId)
  window.removeEventListener('resize', onWindowResize)

  if (renderer && renderer.domElement) {
    renderer.domElement.remove()
    renderer.dispose()
  }
})
</script>
