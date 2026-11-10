import 'package:flutter/material.dart';

enum AppLanguage { en, ur }

class AppStrings {
  static AppLanguage currentLanguage = AppLanguage.en;

  static bool get isRtl => currentLanguage == AppLanguage.ur;
  static TextDirection get textDirection => isRtl ? TextDirection.rtl : TextDirection.ltr;

  static String get appTitle => currentLanguage == AppLanguage.ur ? 'بگ لائیو مارکیٹ پلیس' : 'Big Live Marketplace';
  
  // Navigation
  static String get tabHome => currentLanguage == AppLanguage.ur ? 'ہوم' : 'Home';
  static String get tabCategories => currentLanguage == AppLanguage.ur ? 'کیٹیگریز' : 'Categories';
  static String get tabSell => currentLanguage == AppLanguage.ur ? 'بیچیں' : 'Sell';
  static String get tabChats => currentLanguage == AppLanguage.ur ? 'چیٹ' : 'Chats';
  static String get tabProfile => currentLanguage == AppLanguage.ur ? 'پروفائل' : 'Profile';

  // Feed & Listings
  static String get freshRecommendations => currentLanguage == AppLanguage.ur ? 'تازہ ترین اشتہارات' : 'Fresh Recommendations';
  static String get noListingsFound => currentLanguage == AppLanguage.ur ? 'کوئی اشتہار نہیں ملا' : 'No active listings found';
  static String get postFirstAd => currentLanguage == AppLanguage.ur ? 'پہلا اشتہار پوسٹ کریں' : 'Be the first to post an item for sale!';

  // Auth & Profile
  static String get signIn => currentLanguage == AppLanguage.ur ? 'لاگ ان کریں' : 'Sign In';
  static String get signUp => currentLanguage == AppLanguage.ur ? 'رجسٹر کریں' : 'Register';
  static String get signOut => currentLanguage == AppLanguage.ur ? 'لاگ آؤٹ' : 'Sign Out';
  static String get verificationBadge => currentLanguage == AppLanguage.ur ? 'تصدیق شدہ سیلر' : 'Verified Seller';
}
