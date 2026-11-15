import 'dart:math';
import 'package:flutter/material.dart';

class WithdrawalBottomSheet extends StatefulWidget {
  final int availableMinor;
  final Function(int amountMinor, String method, String beneficiary, String idempotencyKey) onWithdraw;

  const WithdrawalBottomSheet({
    super.key,
    required this.availableMinor,
    required this.onWithdraw,
  });

  @override
  State<WithdrawalBottomSheet> createState() => _WithdrawalBottomSheetState();
}

class _WithdrawalBottomSheetState extends State<WithdrawalBottomSheet> {
  String _selectedMethod = 'jazzcash';
  final _amountController = TextEditingController();
  final _accountController = TextEditingController();
  final _nameController = TextEditingController();
  bool _isSubmitting = false;
  String? _errorMessage;

  @override
  void dispose() {
    _amountController.dispose();
    _accountController.dispose();
    _nameController.dispose();
    super.dispose();
  }

  void _submit() {
    final amountText = _amountController.text.trim();
    final accountText = _accountController.text.trim();
    final nameText = _nameController.text.trim();

    if (amountText.isEmpty || accountText.isEmpty) {
      setState(() {
        _errorMessage = 'Please enter amount and account number';
      });
      return;
    }

    final amountPkr = int.tryParse(amountText);
    if (amountPkr == null || amountPkr < 100) {
      setState(() {
        _errorMessage = 'Minimum withdrawal amount is PKR 100';
      });
      return;
    }

    final amountMinor = amountPkr * 100;
    if (amountMinor > widget.availableMinor) {
      setState(() {
        _errorMessage = 'Amount exceeds available balance';
      });
      return;
    }

    setState(() {
      _errorMessage = null;
      _isSubmitting = true;
    });

    // Generate unique idempotency key
    final randomSuffix = Random().nextInt(999999);
    final idempotencyKey = 'wd_${DateTime.now().millisecondsSinceEpoch}_$randomSuffix';
    final beneficiary = '$_selectedMethod:$accountText${nameText.isNotEmpty ? ' ($nameText)' : ''}';

    widget.onWithdraw(amountMinor, _selectedMethod, beneficiary, idempotencyKey);

    Navigator.pop(context);
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(
        content: Row(
          children: [
            const Icon(Icons.check_circle_rounded, color: Color(0xFF34D399)),
            const SizedBox(width: 8),
            Expanded(
              child: Text(
                'Withdrawal of PKR $amountPkr submitted for finance review!',
                style: const TextStyle(fontWeight: FontWeight.w600),
              ),
            ),
          ],
        ),
        backgroundColor: const Color(0xFF0F172A),
        behavior: SnackBarBehavior.floating,
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    final availablePkr = (widget.availableMinor / 100.0).toStringAsFixed(0);

    return Container(
      decoration: const BoxDecoration(
        color: Color(0xFF0B0F19),
        borderRadius: BorderRadius.vertical(top: Radius.circular(24)),
      ),
      padding: EdgeInsets.fromLTRB(
        20,
        16,
        20,
        MediaQuery.of(context).viewInsets.bottom + 24,
      ),
      child: SingleChildScrollView(
        child: Column(
          mainAxisSize: MainAxisSize.min,
          crossAxisAlignment: CrossAxisAlignment.stretch,
          children: [
            Center(
              child: Container(
                width: 44,
                height: 4,
                decoration: BoxDecoration(
                  color: Colors.white24,
                  borderRadius: BorderRadius.circular(2),
                ),
              ),
            ),
            const SizedBox(height: 16),

            // Header
            Row(
              children: [
                Container(
                  padding: const EdgeInsets.all(10),
                  decoration: BoxDecoration(
                    color: const Color(0xFF34D399).withValues(alpha: 0.15),
                    borderRadius: BorderRadius.circular(12),
                  ),
                  child: const Icon(Icons.account_balance_wallet_rounded, color: Color(0xFF34D399), size: 24),
                ),
                const SizedBox(width: 12),
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      const Text(
                        'Withdraw Earnings (رقم نکلوائیں)',
                        style: TextStyle(
                          fontSize: 17,
                          fontWeight: FontWeight.w800,
                          color: Colors.white,
                        ),
                      ),
                      Text(
                        'Available Balance: PKR $availablePkr',
                        style: const TextStyle(
                          fontSize: 12,
                          color: Color(0xFF34D399),
                          fontWeight: FontWeight.w700,
                        ),
                      ),
                    ],
                  ),
                ),
                GestureDetector(
                  onTap: () => Navigator.pop(context),
                  child: const Icon(Icons.close_rounded, color: Colors.white54),
                ),
              ],
            ),
            const SizedBox(height: 16),

            // Method Selector
            const Text(
              'Select Payment Method',
              style: TextStyle(fontSize: 12, fontWeight: FontWeight.w700, color: Color(0xFF94A3B8)),
            ),
            const SizedBox(height: 8),
            Row(
              children: [
                _buildMethodChip('jazzcash', 'JazzCash', Icons.phone_android_rounded),
                const SizedBox(width: 8),
                _buildMethodChip('easypaisa', 'Easypaisa', Icons.account_balance_wallet_rounded),
                const SizedBox(width: 8),
                _buildMethodChip('bank', 'Bank Transfer', Icons.account_balance_rounded),
              ],
            ),
            const SizedBox(height: 14),

            // Amount field
            TextField(
              controller: _amountController,
              keyboardType: TextInputType.number,
              style: const TextStyle(color: Colors.white, fontSize: 14),
              decoration: InputDecoration(
                labelText: 'Amount (PKR) - Min 100',
                labelStyle: const TextStyle(color: Color(0xFF94A3B8)),
                prefixText: 'PKR ',
                prefixStyle: const TextStyle(color: Color(0xFF38BDF8), fontWeight: FontWeight.bold),
                filled: true,
                fillColor: const Color(0xFF131B2E),
                border: OutlineInputBorder(
                  borderRadius: BorderRadius.circular(12),
                  borderSide: const BorderSide(color: Color(0xFF334155)),
                ),
                enabledBorder: OutlineInputBorder(
                  borderRadius: BorderRadius.circular(12),
                  borderSide: const BorderSide(color: Color(0xFF334155)),
                ),
                focusedBorder: OutlineInputBorder(
                  borderRadius: BorderRadius.circular(12),
                  borderSide: const BorderSide(color: Color(0xFF38BDF8)),
                ),
              ),
            ),
            const SizedBox(height: 12),

            // Account / Phone Number
            TextField(
              controller: _accountController,
              keyboardType: TextInputType.phone,
              style: const TextStyle(color: Colors.white, fontSize: 14),
              decoration: InputDecoration(
                labelText: _selectedMethod == 'bank' ? 'Account Number / IBAN' : 'Mobile Account Number (03xxxxxxxxx)',
                labelStyle: const TextStyle(color: Color(0xFF94A3B8)),
                filled: true,
                fillColor: const Color(0xFF131B2E),
                border: OutlineInputBorder(
                  borderRadius: BorderRadius.circular(12),
                  borderSide: const BorderSide(color: Color(0xFF334155)),
                ),
                enabledBorder: OutlineInputBorder(
                  borderRadius: BorderRadius.circular(12),
                  borderSide: const BorderSide(color: Color(0xFF334155)),
                ),
                focusedBorder: OutlineInputBorder(
                  borderRadius: BorderRadius.circular(12),
                  borderSide: const BorderSide(color: Color(0xFF38BDF8)),
                ),
              ),
            ),
            const SizedBox(height: 12),

            // Account Title
            TextField(
              controller: _nameController,
              style: const TextStyle(color: Colors.white, fontSize: 14),
              decoration: InputDecoration(
                labelText: 'Account Title / Beneficiary Name (Optional)',
                labelStyle: const TextStyle(color: Color(0xFF94A3B8)),
                filled: true,
                fillColor: const Color(0xFF131B2E),
                border: OutlineInputBorder(
                  borderRadius: BorderRadius.circular(12),
                  borderSide: const BorderSide(color: Color(0xFF334155)),
                ),
                enabledBorder: OutlineInputBorder(
                  borderRadius: BorderRadius.circular(12),
                  borderSide: const BorderSide(color: Color(0xFF334155)),
                ),
                focusedBorder: OutlineInputBorder(
                  borderRadius: BorderRadius.circular(12),
                  borderSide: const BorderSide(color: Color(0xFF38BDF8)),
                ),
              ),
            ),

            if (_errorMessage != null) ...[
              const SizedBox(height: 8),
              Text(
                _errorMessage!,
                style: const TextStyle(color: Color(0xFFF87171), fontSize: 12),
              ),
            ],

            const SizedBox(height: 16),

            // Submit Button
            ElevatedButton(
              onPressed: _isSubmitting ? null : _submit,
              style: ElevatedButton.styleFrom(
                backgroundColor: const Color(0xFF059669),
                foregroundColor: Colors.white,
                padding: const EdgeInsets.symmetric(vertical: 14),
                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
              ),
              child: _isSubmitting
                  ? const SizedBox(
                      height: 20,
                      width: 20,
                      child: CircularProgressIndicator(color: Colors.white, strokeWidth: 2),
                    )
                  : const Text(
                      'Confirm Withdrawal Request',
                      style: TextStyle(fontWeight: FontWeight.w700, fontSize: 14),
                    ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildMethodChip(String method, String label, IconData icon) {
    final isSelected = _selectedMethod == method;
    return Expanded(
      child: GestureDetector(
        onTap: () => setState(() => _selectedMethod = method),
        child: Container(
          padding: const EdgeInsets.symmetric(vertical: 10, horizontal: 4),
          decoration: BoxDecoration(
            color: isSelected ? const Color(0xFF38BDF8).withValues(alpha: 0.15) : const Color(0xFF131B2E),
            borderRadius: BorderRadius.circular(10),
            border: Border.all(
              color: isSelected ? const Color(0xFF38BDF8) : const Color(0xFF334155),
              width: isSelected ? 1.5 : 1.0,
            ),
          ),
          child: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              Icon(icon, size: 18, color: isSelected ? const Color(0xFF38BDF8) : const Color(0xFF94A3B8)),
              const SizedBox(height: 4),
              Text(
                label,
                style: TextStyle(
                  fontSize: 10,
                  fontWeight: FontWeight.w700,
                  color: isSelected ? Colors.white : const Color(0xFF94A3B8),
                ),
                maxLines: 1,
                overflow: TextOverflow.ellipsis,
              ),
            ],
          ),
        ),
      ),
    );
  }
}
