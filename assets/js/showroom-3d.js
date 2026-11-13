import * as THREE from 'https://cdn.jsdelivr.net/npm/three@0.180.0/build/three.module.js';

const mount = document.querySelector('#showroomScene');

if (mount) {
  const scene = new THREE.Scene();
  scene.background = new THREE.Color('#24211c');
  scene.fog = new THREE.Fog('#24211c', 14, 28);

  const camera = new THREE.PerspectiveCamera(38, 1, 0.1, 60);
  const renderer = new THREE.WebGLRenderer({ antialias: true, powerPreference: 'low-power', preserveDrawingBuffer: true });
  renderer.setPixelRatio(Math.min(window.devicePixelRatio || 1, 1.8));
  renderer.shadowMap.enabled = true;
  renderer.shadowMap.type = THREE.PCFSoftShadowMap;
  renderer.outputColorSpace = THREE.SRGBColorSpace;
  renderer.toneMapping = THREE.ACESFilmicToneMapping;
  renderer.toneMappingExposure = 1.12;
  renderer.domElement.setAttribute('aria-hidden', 'true');
  mount.prepend(renderer.domElement);

  const room = new THREE.Group();
  scene.add(room);

  const material = (color, roughness = 0.76, metalness = 0) =>
    new THREE.MeshStandardMaterial({ color, roughness, metalness });

  const plaster = material('#ddd0b7');
  const plasterShade = material('#bda98a');
  const walnut = material('#5b3925', 0.44);
  const walnutLight = material('#875e3d', 0.46);
  const brass = material('#b9965b', 0.32, 0.62);
  const olive = material('#53634f');
  const sage = material('#8a9179');
  const ivory = material('#e9dfcc');
  const glass = new THREE.MeshPhysicalMaterial({
    color: '#b7c1bd', roughness: 0.2, metalness: 0.05, transparent: true, opacity: 0.5
  });

  const box = (parent, name, size, position, surface, castShadow = true) => {
    const mesh = new THREE.Mesh(new THREE.BoxGeometry(...size), surface);
    mesh.name = name;
    mesh.position.set(...position);
    mesh.castShadow = castShadow;
    mesh.receiveShadow = true;
    parent.add(mesh);
    return mesh;
  };

  const cylinder = (parent, name, top, bottom, height, position, surface, segments = 20) => {
    const mesh = new THREE.Mesh(new THREE.CylinderGeometry(top, bottom, height, segments), surface);
    mesh.name = name;
    mesh.position.set(...position);
    mesh.castShadow = true;
    mesh.receiveShadow = true;
    parent.add(mesh);
    return mesh;
  };

  const floor = new THREE.Mesh(new THREE.PlaneGeometry(12, 10), material('#795a3c', 0.58));
  floor.rotation.x = -Math.PI / 2;
  floor.position.y = -0.035;
  floor.receiveShadow = true;
  room.add(floor);

  box(room, 'Back plaster wall', [12, 5.2, 0.22], [0, 2.55, -4.85], plaster, false);
  box(room, 'Left plaster return', [0.22, 5.2, 9.5], [-5.9, 2.55, 0], plasterShade, false);
  box(room, 'Ceiling header', [12, 0.28, 0.32], [0, 5.08, -4.68], walnut, false);
  box(room, 'Brass wall trim', [11.8, 0.12, 0.1], [0, 0.92, -4.69], brass, false);

  for (let index = 0; index < 3; index += 1) {
    const x = -3.55 + index * 2.55;
    box(room, `Window glass ${index}`, [2.05, 2.45, 0.08], [x, 3.05, -4.7], glass, false);
    box(room, `Window mullion ${index}`, [0.055, 2.55, 0.09], [x, 3.05, -4.61], brass, false);
    box(room, `Window sill ${index}`, [2.13, 0.09, 0.18], [x, 1.78, -4.62], walnutLight, false);
    box(room, `Window head ${index}`, [2.13, 0.08, 0.16], [x, 4.32, -4.62], walnutLight, false);
  }

  const rug = new THREE.Mesh(new THREE.BoxGeometry(6.6, 0.07, 4.3), material('#71806a'));
  rug.position.set(-0.2, 0.025, 0.8);
  rug.receiveShadow = true;
  rug.castShadow = true;
  room.add(rug);
  box(room, 'Rug border', [6.32, 0.018, 0.035], [-0.2, 0.067, -1.32], brass, false);
  box(room, 'Rug border', [6.32, 0.018, 0.035], [-0.2, 0.067, 2.92], brass, false);

  const sofa = new THREE.Group();
  sofa.position.set(-1.65, 0.12, -0.05);
  room.add(sofa);
  box(sofa, 'Sofa plinth', [3.15, 0.38, 1.18], [0, 0.43, 0], walnut);
  box(sofa, 'Sofa back', [3.12, 1.02, 0.36], [0, 1.02, -0.45], olive);
  box(sofa, 'Sofa arm left', [0.38, 0.78, 1.12], [-1.4, 0.78, 0.02], olive);
  box(sofa, 'Sofa arm right', [0.38, 0.78, 1.12], [1.4, 0.78, 0.02], olive);
  for (let index = 0; index < 3; index += 1) {
    const x = -0.93 + index * 0.93;
    box(sofa, `Seat cushion ${index}`, [0.86, 0.22, 0.82], [x, 0.72, 0.08], index === 1 ? ivory : sage);
    box(sofa, `Back cushion ${index}`, [0.88, 0.75, 0.2], [x, 1.18, -0.2], index === 1 ? sage : ivory);
  }

  const coffeeTable = new THREE.Group();
  coffeeTable.position.set(-1.1, 0.1, 1.65);
  room.add(coffeeTable);
  box(coffeeTable, 'Coffee table top', [2.05, 0.14, 0.98], [0, 0.62, 0], walnutLight);
  for (const x of [-0.82, 0.82]) {
    for (const z of [-0.33, 0.33]) {
      box(coffeeTable, 'Coffee table leg', [0.09, 0.58, 0.09], [x, 0.31, z], brass);
    }
  }
  cylinder(coffeeTable, 'Ceramic vessel', 0.12, 0.16, 0.35, [-0.42, 0.86, 0], ivory);
  cylinder(coffeeTable, 'Vessel neck', 0.07, 0.1, 0.13, [-0.42, 1.1, 0], ivory);

  const dining = new THREE.Group();
  dining.position.set(2.4, 0, -0.4);
  room.add(dining);
  box(dining, 'Dining table top', [2.65, 0.16, 1.38], [0, 1.38, 0], walnutLight);
  for (const x of [-1.03, 1.03]) {
    for (const z of [-0.48, 0.48]) {
      box(dining, 'Dining table leg', [0.12, 1.32, 0.12], [x, 0.68, z], walnut);
    }
  }

  const makeChair = (x, z, rotation = 0) => {
    const chair = new THREE.Group();
    chair.position.set(x, 0, z);
    chair.rotation.y = rotation;
    dining.add(chair);
    box(chair, 'Dining chair seat', [0.72, 0.14, 0.68], [0, 0.76, 0], ivory);
    box(chair, 'Dining chair back', [0.72, 0.86, 0.12], [0, 1.22, -0.28], olive);
    for (const chairX of [-0.28, 0.28]) {
      for (const chairZ of [-0.23, 0.23]) {
        box(chair, 'Dining chair leg', [0.07, 0.72, 0.07], [chairX, 0.36, chairZ], walnut);
      }
    }
  };
  makeChair(-0.1, 1.15, Math.PI);
  makeChair(0.1, -1.15, 0);
  makeChair(-1.75, 0, Math.PI / 2);
  makeChair(1.75, 0, -Math.PI / 2);

  const bed = new THREE.Group();
  bed.position.set(-3.55, 0, -2.05);
  bed.scale.setScalar(0.58);
  room.add(bed);
  box(bed, 'Bed frame', [2.55, 0.42, 3.25], [0, 0.47, 0], walnut);
  box(bed, 'Bed mattress', [2.43, 0.34, 3.12], [0, 0.82, 0], ivory);
  box(bed, 'Bed headboard', [2.64, 1.85, 0.2], [0, 1.2, -1.55], walnutLight);
  box(bed, 'Bed cover', [2.38, 0.12, 1.7], [0, 1.04, 0.62], olive);
  for (const x of [-0.65, 0.65]) {
    box(bed, 'Bed pillow', [0.85, 0.16, 0.56], [x, 1.03, -0.9], ivory);
  }

  const planter = new THREE.Group();
  planter.position.set(-4.45, 0.05, -2.35);
  room.add(planter);
  cylinder(planter, 'Planter', 0.42, 0.3, 0.72, [0, 0.38, 0], walnutLight);
  for (let index = 0; index < 7; index += 1) {
    const angle = index * Math.PI * 2 / 7;
    const leaf = new THREE.Mesh(new THREE.SphereGeometry(0.18, 12, 10), olive);
    leaf.scale.set(0.7, 2.4, 0.65);
    leaf.position.set(Math.cos(angle) * 0.3, 1.25 + (index % 3) * 0.12, Math.sin(angle) * 0.25);
    leaf.rotation.z = Math.cos(angle) * 0.5;
    leaf.castShadow = true;
    planter.add(leaf);
  }

  const pendant = new THREE.Group();
  pendant.position.set(2.35, 0, -0.1);
  room.add(pendant);
  cylinder(pendant, 'Pendant cord', 0.012, 0.012, 1.25, [0, 4.35, 0], brass, 8);
  const shade = new THREE.Mesh(new THREE.ConeGeometry(0.52, 0.42, 32, 1, true), brass);
  shade.position.set(0, 3.68, 0);
  shade.rotation.x = Math.PI;
  shade.castShadow = true;
  pendant.add(shade);
  const bulb = new THREE.PointLight('#ffdba4', 42, 6, 2);
  bulb.position.set(0, 3.38, 0);
  pendant.add(bulb);

  scene.add(new THREE.HemisphereLight('#f5e8d2', '#4b3929', 2.1));
  const keyLight = new THREE.DirectionalLight('#ffe3b8', 3.2);
  keyLight.position.set(-4, 9, 6);
  keyLight.castShadow = true;
  keyLight.shadow.mapSize.set(1024, 1024);
  keyLight.shadow.camera.left = -9;
  keyLight.shadow.camera.right = 9;
  keyLight.shadow.camera.top = 9;
  keyLight.shadow.camera.bottom = -9;
  keyLight.shadow.bias = -0.0004;
  scene.add(keyLight);

  let baseAngle = 0.12;
  let pointerOffset = 0;
  let dragStartX = 0;
  let dragging = false;
  let visible = true;
  let frameId = 0;
  const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  const resize = () => {
    const bounds = mount.getBoundingClientRect();
    if (!bounds.width || !bounds.height) return;
    camera.aspect = bounds.width / bounds.height;
    camera.position.set(Math.sin(baseAngle + pointerOffset) * 16.5, 8.8, Math.cos(baseAngle + pointerOffset) * 16.5);
    camera.lookAt(0, 1.5, -0.15);
    camera.updateProjectionMatrix();
    renderer.setSize(bounds.width, bounds.height, false);
    renderer.render(scene, camera);
  };

  const resizeObserver = new ResizeObserver(resize);
  resizeObserver.observe(mount);
  mount.addEventListener('pointerdown', (event) => {
    if (event.button !== 0) return;
    dragging = true;
    dragStartX = event.clientX;
    mount.setPointerCapture(event.pointerId);
  });
  mount.addEventListener('pointermove', (event) => {
    if (!dragging) return;
    pointerOffset = THREE.MathUtils.clamp(pointerOffset + (event.clientX - dragStartX) * 0.005, -0.72, 0.72);
    dragStartX = event.clientX;
  });
  mount.addEventListener('pointerup', () => { dragging = false; });
  mount.addEventListener('pointercancel', () => { dragging = false; });

  const visibilityObserver = new IntersectionObserver(([entry]) => { visible = entry.isIntersecting; }, { threshold: 0.08 });
  visibilityObserver.observe(mount);
  const clock = new THREE.Clock();

  const render = () => {
    frameId = window.requestAnimationFrame(render);
    const delta = Math.min(clock.getDelta(), 0.05);
    if (!visible || document.hidden) return;
    if (!dragging && !reducedMotion) baseAngle += delta * 0.045;
    camera.position.set(Math.sin(baseAngle + pointerOffset) * 16.5, 8.8, Math.cos(baseAngle + pointerOffset) * 16.5);
    camera.lookAt(0, 1.5, -0.15);
    renderer.render(scene, camera);
  };

  resize();
  mount.classList.add('is-ready');
  render();

  window.addEventListener('pagehide', () => {
    window.cancelAnimationFrame(frameId);
    resizeObserver.disconnect();
    visibilityObserver.disconnect();
    renderer.dispose();
  }, { once: true });
}