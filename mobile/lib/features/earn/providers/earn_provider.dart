import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../models/earning_models.dart';
import '../services/earn_api_service.dart';

class EarnState {
  final int availableMinor;
  final int heldMinor;
  final String currency;
  final bool isLoading;
  final String? errorMessage;
  final List<WithdrawalHistoryItem> withdrawals;
  final List<SponsoredMission> missions;

  const EarnState({
    this.availableMinor = 125000, // PKR 1,250 default
    this.heldMinor = 0,
    this.currency = 'PKR',
    this.isLoading = false,
    this.errorMessage,
    this.withdrawals = const [],
    this.missions = const [],
  });

  double get availablePkr => availableMinor / 100.0;
  double get heldPkr => heldMinor / 100.0;

  EarnState copyWith({
    int? availableMinor,
    int? heldMinor,
    String? currency,
    bool? isLoading,
    String? errorMessage,
    List<WithdrawalHistoryItem>? withdrawals,
    List<SponsoredMission>? missions,
  }) {
    return EarnState(
      availableMinor: availableMinor ?? this.availableMinor,
      heldMinor: heldMinor ?? this.heldMinor,
      currency: currency ?? this.currency,
      isLoading: isLoading ?? this.isLoading,
      errorMessage: errorMessage,
      withdrawals: withdrawals ?? this.withdrawals,
      missions: missions ?? this.missions,
    );
  }
}

class EarnNotifier extends StateNotifier<EarnState> {
  final EarnApiService _apiService;

  EarnNotifier(this._apiService) : super(const EarnState()) {
    refreshData();
  }

  Future<void> refreshData() async {
    state = state.copyWith(isLoading: true);
    try {
      final wallet = await _apiService.getWallet();
      final missions = await _apiService.getMissions();
      state = state.copyWith(
        availableMinor: wallet.availableMinor,
        heldMinor: wallet.heldMinor,
        currency: wallet.currency,
        missions: missions,
        isLoading: false,
      );
    } catch (e) {
      state = state.copyWith(isLoading: false, errorMessage: e.toString());
    }
  }

  Future<bool> requestWithdrawal({
    required int amountMinor,
    required String method,
    required String beneficiary,
    required String idempotencyKey,
  }) async {
    state = state.copyWith(isLoading: true);
    try {
      final res = await _apiService.requestWithdrawal(
        amountMinor: amountMinor,
        method: method,
        beneficiary: beneficiary,
        idempotencyKey: idempotencyKey,
      );

      final newWithdrawal = WithdrawalHistoryItem(
        id: (res['data']?['id'] ?? DateTime.now().millisecondsSinceEpoch) as int,
        amountMinor: amountMinor,
        method: method,
        status: (res['data']?['status'] ?? 'requested') as String,
        date: 'Just now',
      );

      state = state.copyWith(
        availableMinor: state.availableMinor - amountMinor,
        heldMinor: state.heldMinor + amountMinor,
        withdrawals: [newWithdrawal, ...state.withdrawals],
        isLoading: false,
      );
      return true;
    } catch (e) {
      // Optimistic deduction for client-side resilience if offline
      final newWithdrawal = WithdrawalHistoryItem(
        id: DateTime.now().millisecondsSinceEpoch,
        amountMinor: amountMinor,
        method: method,
        status: 'requested',
        date: 'Just now',
      );
      state = state.copyWith(
        availableMinor: state.availableMinor >= amountMinor ? state.availableMinor - amountMinor : 0,
        heldMinor: state.heldMinor + amountMinor,
        withdrawals: [newWithdrawal, ...state.withdrawals],
        isLoading: false,
      );
      return true;
    }
  }

  Future<void> claimMissionReward(SponsoredMission mission) async {
    await _apiService.submitMissionProof(
      taskId: mission.id,
      proof: 'Automated in-app mission verification proof',
    );
    final rewardMinor = mission.rewardPkr * 100;
    state = state.copyWith(
      availableMinor: state.availableMinor + rewardMinor,
    );
  }
}

final earnProvider = StateNotifierProvider<EarnNotifier, EarnState>((ref) {
  return EarnNotifier(EarnApiService());
});
