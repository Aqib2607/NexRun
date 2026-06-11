export type Category = {
  slug: string;
  name: string;
  tagline: string;
  image: string;
};

export type Product = {
  id: string;
  slug: string;
  name: string;
  category: string;
  price: number;
  compareAt?: number;
  rating: number;
  reviewCount: number;
  colors: string[];
  sizes: string[];
  images: string[];
  description: string;
  badge?: "New" | "Bestseller" | "Limited" | "Sale";
  isNew?: boolean;
  isBestseller?: boolean;
};

export type CartItem = {
  productId: string;
  slug: string;
  name: string;
  image: string;
  price: number;
  color: string;
  size: string;
  qty: number;
};
