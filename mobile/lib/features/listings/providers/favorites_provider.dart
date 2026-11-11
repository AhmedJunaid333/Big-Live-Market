import 'dart:convert';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:flutter_secure_storage/flutter_secure_storage.dart';

class FavoritesNotifier extends StateNotifier<Set<int>> {
  final FlutterSecureStorage _storage = const FlutterSecureStorage();
  static const _key = 'user_favorite_listing_ids';

  FavoritesNotifier() : super(<int>{}) {
    _loadFavorites();
  }

  Future<void> _loadFavorites() async {
    try {
      final jsonStr = await _storage.read(key: _key);
      if (jsonStr != null && jsonStr.isNotEmpty) {
        final List<dynamic> list = jsonDecode(jsonStr);
        state = list.map((e) => (e as num).toInt()).toSet();
      }
    } catch (_) {}
  }

  Future<void> toggleFavorite(int listingId) async {
    final next = Set<int>.from(state);
    if (next.contains(listingId)) {
      next.remove(listingId);
    } else {
      next.add(listingId);
    }
    state = next;
    try {
      await _storage.write(key: _key, value: jsonEncode(next.toList()));
    } catch (_) {}
  }

  bool isFavorite(int listingId) => state.contains(listingId);
}

final favoritesProvider = StateNotifierProvider<FavoritesNotifier, Set<int>>((ref) {
  return FavoritesNotifier();
});
