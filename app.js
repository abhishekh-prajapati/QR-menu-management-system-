
let currentLang = "en";
let activeCategory = "veg";
let menuData = {};
let cart = {};

/* LOAD MENU FROM PHP API */
async function loadMenuJSON(lang) {
  try {
    const res = await fetch(`api/get_menu.php?lang=${lang}`);
    if (!res.ok) {
      throw new Error(`HTTP error! status: ${res.status}`);
    }

    const data = await res.json();

    // Check if API returned an error
    if (data.error) {
      console.error("API Error:", data.error);
      showMenuError(data.error);
      return;
    }

    menuData = data;

    // Check if menu is empty
    const totalItems = Object.values(menuData).reduce((sum, cat) => sum + cat.length, 0);
    if (totalItems === 0) {
      showMenuError("No menu items found. Please contact support.");
      return;
    }

    // Assign consistent ratings/votes once on load
    Object.keys(menuData).forEach(cat => {
      menuData[cat].forEach(item => {
        if (!item.rating) {
          item.rating = (Math.random() * (4.8 - 3.9) + 3.9).toFixed(1);
        }
        if (!item.votes) {
          item.votes = Math.floor(Math.random() * 900) + 50;
        }
      });
    });
    currentLang = lang;
    updateLanguageUI(lang);

    renderCategories();
    renderMenu(activeCategory);
  } catch (err) {
    console.error("Menu loading error:", err);
    showMenuError("Failed to load menu. Please refresh the page or contact support.");
  }
}

/* SHOW MENU ERROR */
function showMenuError(message) {
  const menuBox = document.getElementById("menu");
  if (menuBox) {
    menuBox.innerHTML = `
      <div style="text-align: center; padding: 60px 20px; background: white; border-radius: 12px; margin: 20px 0;">
        <i class="fas fa-exclamation-triangle" style="font-size: 48px; color: #ea580c; margin-bottom: 20px;"></i>
        <h2 style="color: #0f172a; margin-bottom: 10px;">Unable to Load Menu</h2>
        <p style="color: #64748b; margin-bottom: 20px;">${message}</p>
        <button onclick="location.reload()" style="background: #2563eb; color: white; border: none; padding: 12px 24px; border-radius: 8px; font-weight: 600; cursor: pointer;">
          <i class="fas fa-sync-alt"></i> Refresh Page
        </button>
      </div>
    `;
  }

  // Also hide categories if menu failed
  const categoriesBox = document.querySelector(".categories");
  if (categoriesBox) {
    categoriesBox.innerHTML = "";
  }
}

function updateLanguageUI(lang) {
  // Update buttons
  document.querySelectorAll(".lang-switch button").forEach(btn => {
    btn.classList.toggle("active", btn.dataset.lang === lang);
  });
  // Update dropdown
  const dropdown = document.getElementById("langDropdown");
  if (dropdown) dropdown.value = lang;
}

/* RENDER CATEGORIES */
function renderCategories() {
  const box = document.querySelector(".categories");
  if (!box) return;
  box.innerHTML = "";

  const icons = {
    veg: "fa-leaf",
    nonveg: "fa-drumstick-bite",
    chinese: "fa-bowl-rice",
    south: "fa-utensils",
    snacks: "fa-cookie-bite"
  };

  Object.keys(menuData).forEach(cat => {
    const span = document.createElement("span");
    const iconClass = icons[cat] || "fa-utensils";
    span.setAttribute("data-cat", cat); // Add data attribute for robust matching
    span.innerHTML = `<i class="fas ${iconClass}"></i> ${formatCategory(cat)}`;
    if (cat === activeCategory) span.classList.add("active");

    span.onclick = () => {
      activeCategory = cat;
      renderMenu(cat);
      setActiveCategoryUI(cat);

      const menuEl = document.getElementById("menu");
      if (menuEl) menuEl.scrollIntoView({ behavior: "smooth", block: "start" });
    };

    box.appendChild(span);
  });
}

function setActiveCategoryUI(cat) {
  if (!cat) return;
  const targetCat = String(cat).toLowerCase().trim();

  document.querySelectorAll(".categories span").forEach(span => {
    const spanCat = String(span.getAttribute("data-cat") || "").toLowerCase().trim();
    span.classList.toggle("active", spanCat === targetCat);
  });
}

/* RENDER MENU (ZOMATO STYLE) */
function renderMenu(cat) {
  const box = document.getElementById("menu");
  if (!box) return;

  let html = `<div class="menu-section" data-cat="${cat}"></div>`;

  menuData[cat].forEach((item, i) => {
    const qty = cart[item.name]?.qty || 0;

    // Dietary Indicators (Professional Minimalist)
    const dietIcon = item.is_veg == 1
      ? `<span class="diet-icon veg"><i class="fas fa-circle"></i></span>`
      : `<span class="diet-icon nonveg"><i class="fas fa-play fa-rotate-270" style="font-size: 7px;"></i></span>`;

    const spicyIcon = item.is_spicy == 1
      ? `<span class="spicy-icon"><i class="fas fa-pepper-hot"></i></span>`
      : '';

    const bestsellerTag = item.bestseller == 1
      ? `<span class="tag-bestseller">Signature</span>`
      : '';

    const rating = item.rating;
    const votes = item.votes;

    html += `
      <div class="menu-item-z" id="item-${cat}-${i}">
        <div class="menu-left">
          <div class="menu-meta" style="margin-bottom: 8px;">
            ${dietIcon}
            ${spicyIcon}
            ${bestsellerTag}
          </div>
          <h3 class="menu-title" style="margin-bottom: 6px;">${item.name}</h3>
          <div class="menu-rating" style="margin-bottom: 10px;">
            <div class="stars">${rating} <i class="fas fa-star" style="font-size: 9px;"></i></div>
            <span class="votes">(${votes} reviews)</span>
          </div>
          <div class="menu-price" style="font-size: 18px; font-weight: 800; color: var(--text-dark);">₹${item.price}</div>
          <div class="menu-desc" style="margin-top: 12px; line-height: 1.5; opacity: 0.8;">${item.desc || "Deliciously prepared with fresh ingredients."}</div>
        </div>

        <div class="menu-right" style="position: relative;">
          <div class="img-wrapper" style="width: 140px; height: 140px; border-radius: 20px; box-shadow: 0 8px 20px rgba(0,0,0,0.1);">
             <img src="./images/${item.img}" onerror="this.src='./images/food-placeholder.jpg'" style="width: 100%; height: 100%; object-fit: cover; border-radius: 20px;">
             <div class="add-action-area" style="position: absolute; bottom: -12px; left: 50%; transform: translateX(-50%); width: 100px;">
                ${qty === 0
        ? `<button class="add-btn-z" style="box-shadow: 0 4px 12px rgba(0,0,0,0.2); height: 40px; border-radius: 10px;" onclick="updateCart('${cat}',${i},1)">ADD</button>`
        : `<div class="qty-control-z" style="background: var(--text-dark); color: white; border-radius: 10px; display: flex; align-items: center; justify-content: space-between; height: 40px; box-shadow: 0 4px 12px rgba(0,0,0,0.2); padding: 0 8px;">
                        <button onclick="updateCart('${cat}',${i},-1)" style="background: none; border: none; color: white; font-size: 20px; cursor: pointer;">−</button>
                        <span style="font-weight: 700;">${qty}</span>
                        <button onclick="updateCart('${cat}',${i},1)" style="background: none; border: none; color: white; font-size: 20px; cursor: pointer;">+</button>
                     </div>`
      }
             </div>
          </div>
        </div>
      </div>
      <div class="divider-dashed" style="margin: 0 20px; border-bottom: 1px dashed var(--border-soft);"></div>
    `;
  });
  box.innerHTML = html;
}

/* CART LOGIC */
function updateCart(cat, i, change) {
  const item = menuData[cat][i];
  if (!cart[item.name]) cart[item.name] = { qty: 0, price: item.price };

  cart[item.name].qty += change;
  if (cart[item.name].qty <= 0) delete cart[item.name];

  renderMenu(cat);
  renderCart();
}

// Toggle Collapsible Cart
function toggleCart() {
  const bar = document.getElementById("cartBar");
  bar.classList.toggle("open");
}

function renderCart() {
  const bar = document.getElementById("cartBar");
  const list = document.getElementById("cartList");
  const countEl = document.getElementById("cartCount");
  const totalEl = document.getElementById("cartTotalDisplay");

  if (!bar || !list) return;

  let total = 0;
  let itemCount = 0;
  list.innerHTML = "";

  Object.keys(cart).forEach(name => {
    const qty = cart[name].qty;
    const price = cart[name].price;
    const itemTotal = qty * price;

    total += itemTotal;
    itemCount += qty;

    list.innerHTML += `
      <div class="cart-item-row">
        <span>${name} <span style="font-size:12px;color:#94a3b8;">x${qty}</span></span>
        <span style="font-weight:600;">₹${itemTotal}</span>
      </div>
    `;
  });

  if (countEl) countEl.innerText = itemCount;
  if (totalEl) totalEl.innerText = "₹" + total;

  // Show bar if items exist
  bar.style.display = total > 0 ? "block" : "none";
}

/* REDIRECT TO PAYMENT */
function placeOrder() {
  if (Object.keys(cart).length === 0) {
    showPopup("Cart is empty", '<i class="fas fa-exclamation-triangle"></i>');
    return;
  }

  // Check if user is verified
  if (typeof window.isVerified !== 'undefined' && !window.isVerified) {
    const detailsSection = document.getElementById("authDetailsSection");
    const otpSection = document.getElementById("otpSection");
    if (detailsSection) detailsSection.style.display = "block";
    if (otpSection) otpSection.style.display = "none";

    document.getElementById("authModal").style.display = "flex";
    return;
  }

  sessionStorage.setItem("aspord_cart", JSON.stringify(cart));
  window.location.href = "payment.php";
}

/* CUSTOM POPUP LOGIC */
function showPopup(msg, icon = '<i class="fas fa-check-circle"></i>') {
  const popup = document.getElementById("customPopup");
  if (!popup) return;

  popup.querySelector(".msg").textContent = msg;
  popup.querySelector(".icon").innerHTML = icon;

  popup.classList.add("show");
  setTimeout(() => {
    popup.classList.remove("show");
  }, 3000);
}

/* LANGUAGE SWITCH EVENT LISTENERS */
document.querySelectorAll(".lang-switch button").forEach(btn => {
  btn.onclick = () => loadMenuJSON(btn.dataset.lang);
});

const langDropdown = document.getElementById("langDropdown");
if (langDropdown) {
  langDropdown.addEventListener("change", () => loadMenuJSON(langDropdown.value));
}

function formatCategory(cat) {
  const c = String(cat).toLowerCase().trim();
  if (c === "nonveg") return "Non Veg";
  return c.charAt(0).toUpperCase() + c.slice(1);
}

/* SCROLL SPY */
window.addEventListener("scroll", () => {
  const sections = document.querySelectorAll(".menu-section");
  let current = activeCategory;

  sections.forEach(sec => {
    const rect = sec.getBoundingClientRect();
    if (rect.top <= 120 && rect.bottom > 120) {
      current = sec.dataset.cat;
    }
  });
  setActiveCategoryUI(current);
});

/* SEARCH LOGIC */
const searchInput = document.getElementById("searchInput");
const suggestionsBox = document.getElementById("searchSuggestions");

if (searchInput && suggestionsBox) {
  searchInput.addEventListener("input", () => {
    const query = searchInput.value.toLowerCase().trim();
    suggestionsBox.innerHTML = "";

    if (!query) {
      suggestionsBox.style.display = "none";
      return;
    }

    let results = [];
    Object.keys(menuData).forEach(category => {
      menuData[category].forEach(item => {
        if (item.name.toLowerCase().includes(query)) {
          results.push({ ...item, category });
        }
      });
    });

    if (results.length === 0) {
      suggestionsBox.innerHTML = '<div style="padding: 16px; font-size: 13px; color: var(--text-muted); text-align: center;">No items found...</div>';
      suggestionsBox.style.display = "block";
      return;
    }

    results.slice(0, 6).forEach(item => {
      const div = document.createElement("div");
      div.className = "suggestion-item";
      div.innerHTML = `
        <div style="display: flex; align-items: center; gap: 12px;">
          <img src="./images/${item.img}" onerror="this.src='./images/food-placeholder.jpg'" 
               style="width: 40px; height: 40px; border-radius: 8px; object-fit: cover; background: #eee;">
          <div class="item-info">
            <span class="item-name">${item.name}</span>
            <span class="item-cat">${formatCategory(item.category)}</span>
          </div>
        </div>
      `;
      div.onclick = (e) => {
        e.stopPropagation();
        suggestionsBox.style.display = "none";
        searchInput.value = "";
        activeCategory = item.category;
        renderMenu(item.category);
        setActiveCategoryUI(item.category);

        setTimeout(() => {
          const titleElements = document.querySelectorAll(".menu-title");
          for (const t of titleElements) {
            if (t.textContent === item.name) {
              const target = t.closest(".menu-item-z");
              if (target) {
                target.scrollIntoView({ behavior: "smooth", block: "center" });
                target.style.background = "#fffbeb";
                target.style.transition = "background 0.5s ease";
                setTimeout(() => target.style.background = "transparent", 1500);
              }
              break;
            }
          }
        }, 100);
      };
      suggestionsBox.appendChild(div);
    });
    suggestionsBox.style.display = "block";
  });

  // Close suggestions when clicking outside
  document.addEventListener("click", (e) => {
    if (!searchInput.contains(e.target) && !suggestionsBox.contains(e.target)) {
      suggestionsBox.style.display = "none";
    }
  });
}

/* INITIAL LOAD */
loadMenuJSON("en");
