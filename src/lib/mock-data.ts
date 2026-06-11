import type { Category, Product } from "@/types";
import runnerBlack from "@/assets/product-runner-black.jpg";
import trainerWhite from "@/assets/product-trainer-white.jpg";
import basketballBlue from "@/assets/product-basketball-blue.jpg";
import apparelBlack from "@/assets/product-apparel-black.jpg";
import catRunning from "@/assets/category-running.jpg";
import catTraining from "@/assets/category-training.jpg";
import catLifestyle from "@/assets/category-lifestyle.jpg";

export const COLORS = ["Black", "White", "Blue", "Red", "Orange"] as const;
export const SIZES = ["XS", "S", "M", "L", "XL", "38", "39", "40", "41", "42"] as const;

export const categories: Category[] = [
  { slug: "running", name: "Running", tagline: "Engineered for distance", image: catRunning },
  { slug: "training", name: "Training", tagline: "Built for the grind", image: catTraining },
  { slug: "lifestyle", name: "Lifestyle", tagline: "Street-ready essentials", image: catLifestyle },
  { slug: "basketball", name: "Basketball", tagline: "Court-tested grip", image: catTraining },
  { slug: "football", name: "Football", tagline: "Pitch performance", image: catRunning },
];

const imageFor = (i: number) =>
  [runnerBlack, trainerWhite, basketballBlue, apparelBlack][i % 4];

const NAMES = [
  "Velocity Pro", "Stride Elite", "Aero Flex", "Court Surge", "Trailblaze",
  "PaceMaker", "Pulse Lite", "Apex Form", "Boost Run", "Edge Trainer",
  "Drift Court", "Glide Max", "Fusion Knit", "Tempo Air", "Rebel Run",
  "Summit X", "Zone Pro", "Cadence", "Volt", "Surge HD",
];

const ADJ = ["Black", "White", "Storm", "Ignite", "Phantom", "Glacier", "Volt", "Ember"];

const categoriesPool = ["running", "training", "lifestyle", "basketball", "football"];

export const products: Product[] = Array.from({ length: 40 }, (_, i) => {
  const base = NAMES[i % NAMES.length];
  const adj = ADJ[(i * 3) % ADJ.length];
  const name = `${base} ${adj}`;
  const slug = name.toLowerCase().replace(/\s+/g, "-") + `-${i + 1}`;
  const category = categoriesPool[i % categoriesPool.length];
  const price = [89, 109, 129, 149, 169, 189, 219, 249][i % 8];
  const compareAt = i % 4 === 0 ? price + 40 : undefined;
  const img = imageFor(i);
  return {
    id: `p_${i + 1}`,
    slug,
    name,
    category,
    price,
    compareAt,
    rating: 3.8 + ((i * 7) % 12) / 10,
    reviewCount: 24 + ((i * 17) % 400),
    colors: i % 2 === 0 ? ["Black", "White", "Orange"] : ["White", "Blue", "Red"],
    sizes: category === "lifestyle" || category === "training" && i % 3 === 0
      ? ["XS", "S", "M", "L", "XL"]
      : ["38", "39", "40", "41", "42"],
    images: [img, imageFor(i + 1), imageFor(i + 2), imageFor(i + 3)],
    description:
      "Precision engineered with responsive cushioning, breathable knit upper, and a high-traction outsole. Designed for athletes who refuse to slow down.",
    badge: i % 7 === 0 ? "New" : i % 5 === 0 ? "Bestseller" : i % 11 === 0 ? "Limited" : compareAt ? "Sale" : undefined,
    isNew: i % 7 === 0,
    isBestseller: i % 5 === 0,
  };
});

export const getProduct = (slug: string) => products.find((p) => p.slug === slug);
export const getByCategory = (slug: string) => products.filter((p) => p.category === slug);
export const newArrivals = products.filter((p) => p.isNew).slice(0, 8);
export const bestSellers = products.filter((p) => p.isBestseller).slice(0, 8);
