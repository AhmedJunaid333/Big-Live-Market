import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

enum AppLanguage { en, ur }

class LanguageNotifier extends StateNotifier<AppLanguage> {
  LanguageNotifier() : super(AppLanguage.en);

  void toggleLanguage() {
    state = state == AppLanguage.en ? AppLanguage.ur : AppLanguage.en;
    AppStrings.currentLanguage = state;
  }

  void setLanguage(AppLanguage lang) {
    state = lang;
    AppStrings.currentLanguage = lang;
  }
}

final languageProvider = StateNotifierProvider<LanguageNotifier, AppLanguage>((ref) {
  return LanguageNotifier();
});

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
  static String get searchPlaceholder => currentLanguage == AppLanguage.ur ? 'تصدیق شدہ اشتہارات تلاش کریں...' : 'Search verified listings...';
  static String get filters => currentLanguage == AppLanguage.ur ? 'فلٹرز' : 'Filters';
  static String get applyFilters => currentLanguage == AppLanguage.ur ? 'فلٹر لاگو کریں' : 'Apply Filters';
  static String get resetFilters => currentLanguage == AppLanguage.ur ? 'ری سیٹ' : 'Reset';

  // Details & Actions
  static String get callSeller => currentLanguage == AppLanguage.ur ? 'کال کریں' : 'Call Seller';
  static String get directChat => currentLanguage == AppLanguage.ur ? 'چیٹ کریں' : 'Direct Chat';
  static String get shareListing => currentLanguage == AppLanguage.ur ? 'شیئر' : 'Share';
  static String get myListings => currentLanguage == AppLanguage.ur ? 'میرے اشتہارات' : 'My Listings';
  static String get favorites => currentLanguage == AppLanguage.ur ? 'محفوظ شدہ اشتہارات' : 'Saved Favorites';
  static String get noFavorites => currentLanguage == AppLanguage.ur ? 'ابھی تک کوئی محفوظ اشتہار نہیں' : 'No saved listings yet';

  // Auth & Profile
  static String get signIn => currentLanguage == AppLanguage.ur ? 'لاگ ان کریں' : 'Sign In';
  static String get signUp => currentLanguage == AppLanguage.ur ? 'نیا اکاؤنٹ بنائیں' : 'Create Account';
  static String get signOut => currentLanguage == AppLanguage.ur ? 'لاگ آؤٹ' : 'Sign Out';
  static String get verificationBadge => currentLanguage == AppLanguage.ur ? 'تصدیق شدہ سیلر' : 'Verified Seller';
  static String get accountVerification => currentLanguage == AppLanguage.ur ? 'شناخت کی تصدیق (KYC)' : 'Account Verification (KYC)';
  static String get securitySessions => currentLanguage == AppLanguage.ur ? 'سیکورٹی اور لاگ ان ڈیوائسز' : 'Security & Active Sessions';
  static String get languageSetting => currentLanguage == AppLanguage.ur ? 'زبان تبدیل کریں (Language)' : 'Change Language / زبان';
}
