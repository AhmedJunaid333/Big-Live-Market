import 'package:flutter_riverpod/flutter_riverpod.dart';

class FilterState {
  final double? minPrice;
  final double? maxPrice;
  final String? condition; // 'new', 'used', 'refurbished', null
  final String? city; // 'Lahore', 'Karachi', etc. null for all
  final String sortBy; // 'newest', 'price_asc', 'price_desc'

  const FilterState({
    this.minPrice,
    this.maxPrice,
    this.condition,
    this.city,
    this.sortBy = 'newest',
  });

  bool get isActive =>
      minPrice != null ||
      maxPrice != null ||
      condition != null ||
      city != null ||
      sortBy != 'newest';

  FilterState copyWith({
    double? Function()? minPrice,
    double? Function()? maxPrice,
    String? Function()? condition,
    String? Function()? city,
    String? sortBy,
  }) {
    return FilterState(
      minPrice: minPrice != null ? minPrice() : this.minPrice,
      maxPrice: maxPrice != null ? maxPrice() : this.maxPrice,
      condition: condition != null ? condition() : this.condition,
      city: city != null ? city() : this.city,
      sortBy: sortBy ?? this.sortBy,
    );
  }

  FilterState reset() => const FilterState();
}

class FilterNotifier extends StateNotifier<FilterState> {
  FilterNotifier() : super(const FilterState());

  void setMinPrice(double? price) {
    state = state.copyWith(minPrice: () => price);
  }

  void setMaxPrice(double? price) {
    state = state.copyWith(maxPrice: () => price);
  }

  void setCondition(String? condition) {
    state = state.copyWith(condition: () => condition);
  }

  void setCity(String? city) {
    state = state.copyWith(city: () => city);
  }

  void setSortBy(String sortBy) {
    state = state.copyWith(sortBy: sortBy);
  }

  void reset() {
    state = const FilterState();
  }
}

final filterProvider = StateNotifierProvider<FilterNotifier, FilterState>((ref) {
  return FilterNotifier();
});
